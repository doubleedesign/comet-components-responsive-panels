<script lang="ts">
import type { PanelItem } from '@/components/ResponsivePanels/types';

export default {
	name: 'Accordion',
	inheritAttrs: true,
	props: {
		panels: {
			type: Array as () => PanelItem[],
			required: true,
		},
		icon: String,
		isResponsive: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			iconHtml: this.icon ? `<i class="${this.icon}"></i>` : '',
			// Replace generic responsive-panel classes with accordion-specific classes if isResponsive is true
			// (allows us to skip all this processing if we didn't come from ResponsivePanels context)
			transformedPanels: this.isResponsive ? this.panels.map((panel: PanelItem) => {
				return {
					summary: {
						...panel.summary,
						classes: panel?.summary?.classes?.map((className: string) => className.replace('responsive-panel', 'accordion__panel')) ?? [],
						title: {
							...panel.summary.title,
							classes: panel?.summary?.title?.classes?.map((className: string) => {
								return className.replace('responsive-panel', 'accordion__panel');
							}) ?? []
						},
						subtitle: {
							...panel?.summary?.subtitle,
							classes: panel?.summary?.subtitle?.classes?.map((className: string) => {
								return className.replace('responsive-panel', 'accordion__panel');
							}) ?? []
						},
					},
					content: {
						...panel.content,
						classes: panel?.content?.classes?.map((className: string) => {
							return className.replace('responsive-panel', 'accordion__panel');
						}) ?? []
					}
				};
			}) : this.panels,
			// Track animation state to help prevent race conditions
			animating: false
		};
	},
	mounted() {
		document.addEventListener('selectionchange', this.watchForSelectedTextAndOpenPanel);
	},
	beforeUnmount() {
		// Clean up event listeners
		document.removeEventListener('selectionchange', this.watchForSelectedTextAndOpenPanel);
	},
	methods: {
		togglePanel(event: Event) {
			event.preventDefault();

			// Prevent subsequent click events while still animating from the first one
			if (this.animating) return;

			const summary = event.currentTarget as HTMLElement;
			const details = summary.parentElement as HTMLDetailsElement;
			const isOpen = details.open;

			if (isOpen) {
				this.animateClose(details);
			}
			else {
				this.animateOpen(details);
			}
		},
		animateOpen(details: HTMLDetailsElement) {
			const content = details.querySelector('.accordion__panel__content') as HTMLElement;
			if (!content) return;

			this.animating = true;

			// Set details.open before measuring height
			details.open = true;

			// Force a reflow to ensure the browser recognises the open state
			void content.offsetHeight;

			// Set initial height to 0
			content.style.height = '0px';
			content.style.overflow = 'hidden';
			content.style.display = 'block';

			// Ensure the browser has processed the height changes before animating
			requestAnimationFrame(() => {
				const height = content.scrollHeight;

				// Start the animation in the next frame
				requestAnimationFrame(() => {
					content.style.height = `${height}px`;

					const onTransitionEnd = () => {
						// Clean up styles after animation completes
						content.style.height = '';
						content.style.overflow = '';
						content.style.display = '';
						content.removeEventListener('transitionend', onTransitionEnd);
						this.animating = false;
					};

					content.addEventListener('transitionend', onTransitionEnd, { once: true });
				});
			});
		},
		animateClose(details: HTMLDetailsElement) {
			const content = details.querySelector('.accordion__panel__content') as HTMLElement;
			if (!content) return;

			this.animating = true;

			// Get current height and set it explicitly so CSS transitions will work
			const height = content.scrollHeight;
			content.style.height = `${height}px`;
			content.style.overflow = 'hidden';

			// Force a reflow to ensure the browser recognises the height change
			void content.offsetHeight;

			// Start the animation in the next frame to ensure the browser has processed the height changes
			requestAnimationFrame(() => {
				content.style.height = '0px';

				const onTransitionEnd = () => {
					content.style.height = '';
					content.style.overflow = '';
					details.open = false;
					content.removeEventListener('transitionend', onTransitionEnd);
					this.animating = false;
				};

				content.addEventListener('transitionend', onTransitionEnd, { once: true });
			});
		},
		// If text in a panel is selected such as via browser 'find in page', switch to that panel
		watchForSelectedTextAndOpenPanel(event: Event) {
			// Don't trigger if another animation is still in progress
			if (this.animating) return;

			const selection = document.getSelection();
			// Get the details element that the selection is in, if any
			const details = selection?.anchorNode?.parentElement?.closest('details');
			// Open it if it's closed
			if (details && !details.open) {
				this.animateOpen(details);
			}
		}
	}
};
</script>

<template>
    <div class="accordion" role="group">
        <details
            class="accordion__panel"
            v-for="(panel, index) in this.transformedPanels"
            :key="index"
        >
            <summary
                :class="panel.summary.classes"
                v-bind="panel.summary.attributes"
                :aria-controls="panel.content.attributes.id"
                @click="(event: MouseEvent) => this.togglePanel(event)"
            >
                <span :class="panel.summary.title.classes"
                      v-bind="panel.summary.title.attributes"
                      v-html="panel.summary.title.content"
                >
                </span>
                <small v-if="panel.summary.subtitle"
                       :class="panel.summary.subtitle.classes"
                       v-bind="panel.summary.subtitle.attributes"
                       v-html="panel.summary.subtitle.content"
                ></small>
                <span v-html="this.iconHtml"></span>
            </summary>
            <div
                :class="panel.content.classes"
                v-bind="panel.content.attributes"
                v-html="panel.content.content"
            ></div>
        </details>
    </div>
</template>

<style lang="css">
/** Sadly, Sass-style BEM nesting e.g., &__ prefix does not work in vanilla CSS */
.accordion {

    .accordion__panel {
        display: contents; /* allows open attribute to be ignored and contents to animate with CSS transitions */

        .accordion__panel__title {
            cursor: pointer;
            transition: all 0.2s linear;
            padding: var(--spacing-sm);
            margin-block-start: var(--spacing-xxs);
            background: color-mix(in srgb, var(--theme-color) 10%, white);
            /* Default: Main title and icon only */
            display: flex;
            align-items: center;
            justify-content: space-between;

            /* Subtitle is optional, so handle differently if it's there */

            &:has(.accordion__panel__title__subtitle) {
                display: grid;
                width: 100%;
                grid-template-columns: 1fr auto;
                grid-template-rows: auto auto;
            }

            .accordion__panel[open] &,
            &:hover, &:focus, &:active {
                background: var(--theme-color);
                color: var(--theme-text-color);
            }

            .accordion__panel__title__main {
                grid-column: 1;
                grid-row: 1;
                font-weight: var(--font-weight-semibold);
            }

            .accordion__panel__title__subtitle {
                grid-column: 1;
                grid-row: 2;
            }

            > span > i, svg {
                grid-column: 2;
                grid-row: 1 / 3;
                transition: all 0.2s linear;
                transform-origin: center center;

                .accordion__panel[open] & {
                    transform: rotate(45deg);
                }
            }

            &::marker {
                display: none;
                visibility: hidden;
                font-size: 0
            }
        }

        .accordion__panel__content {
            transition: height 0.3s ease-in-out;

            > :first-child {
                padding-block-start: var(--spacing-md);
            }

            > :last-child {
                padding-block-end: var(--spacing-md);
            }
        }
    }
}

</style>
