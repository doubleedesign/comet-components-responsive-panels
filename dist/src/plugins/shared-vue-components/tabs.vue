<script lang="ts">
import type { PanelItem } from '../../components/ResponsivePanels/types.ts';

export default {
	name: 'Tabs',
	inheritAttrs: true,
	props: {
		panels: {
			type: Array as () => PanelItem[],
			required: true,
		},
		isResponsive: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			debouncedResize: null,
			// Initially open the first panel
			activePanelIndex: 0,
			// Overall height of the element, to be based on the highest panel
			height: 0,
			// Replace generic responsive-panel classes with tab-specific classes if isResponsive is true
			// (allows us to skip some processing if we didn't come from ResponsivePanels context)
			tabs: this.isResponsive ? this.panels.map((panel: PanelItem) => {
				return ({
					wrapper: {
						...panel.summary,
						classes: panel.summary.classes.map((className: string) => className.replace('responsive-panel', 'tabs__tab-list__item')),
					},
					title: {
						...panel.summary.title,
						classes: panel.summary.title.classes.map((className: string) => className.replace('responsive-panel', 'tabs__tab-list__item')),
					},
					subtitle: {
						...panel?.summary?.subtitle,
						classes: panel?.summary?.subtitle?.classes.map((className: string) => className.replace('responsive-panel', 'tabs__tab-list__item')),
					}
				});
			}) : this.panels.map((panel: PanelItem) => ({
				wrapper: {
					classes: panel.summary.classes,
					attributes: panel.summary.attributes
				},
				title: panel.summary.title,
				subtitle: panel.summary.subtitle
			})),
			contents: this.panels.map((panel: PanelItem) => ({
				...panel.content,
				classes: panel.content.classes.map((className: string) => className.replace('responsive-panel__content', 'tabs__content__tab-panel')),
			})),
			// Get the colour theme to pass down in more specific ways
			colorTheme: this.$attrs['data-color-theme'] || 'primary',
		};
	},
	mounted() {
		document.addEventListener('selectionchange', this.watchForSelectedTextAndOpenPanel);
		// Store a reference to the debounced function rather than applying it directly, so it can be cleaned up later
		this.debouncedResize = this.debounce(() => this.height = this.recalculateHeight(), 200);
		// Calculate initial height
		this.height = this.debouncedResize();
		// Recalculate on resize
		window.addEventListener('resize', this.debouncedResize);
	},
	beforeUnmount() {
		// Clean up event listeners
		document.removeEventListener('selectionchange', this.watchForSelectedTextAndOpenPanel);
		window.removeEventListener('resize', this.debouncedResize);
	},
	methods: {
		recalculateHeight() {
			// Get the height of the panels before any CSS to set the height of the overall element accordingly
			const panels = this.$el.querySelectorAll('[role="tabpanel"]');
			const maxHeight = Array.from(panels).reduce((max: number, panel) => {
				const height = (panel as HTMLElement).scrollHeight;

				return height > max ? height : max;
			}, 0);

			return maxHeight;
		},
		// Open/close panels
		togglePanel(event: Event, index: number) {
			event.preventDefault();
			this.activePanelIndex = index;
		},
		// If text in a panel is selected such as via browser 'find in page', switch to that panel
		watchForSelectedTextAndOpenPanel(event: Event) {
			const selection = document.getSelection();
			// Get the element the selection is in, if any
			const tab = selection?.anchorNode?.parentElement.closest('[role="tabpanel"]');
			if (tab) {
				const index = Array.from(tab.parentElement.children).indexOf(tab);
				this.togglePanel(event, index);
			}
		},
		debounce(func: Function, delay: number) {
			let timerId;

			return function () {
				clearTimeout(timerId);
				timerId = setTimeout(func, delay);
			};
		},
	}
};
</script>

<template>
    <div class="tabs" :style="this.height ? { height: this.height + 'px' } : {}">
        <ul class="tabs__tab-list" role="tablist" :data-background="this.colorTheme">
            <li v-for="(tabItem, index) in this.tabs"
                :key="index"
                class="tabs__tab-list__item"
                v-bind="tabItem.wrapper.attributes"
                role="presentation"
            >
                <!--TODO: Make direct page anchors work, and find a way to make it a relevant ID -->
                <a
                    role="tab"
                    :class="tabItem.wrapper.classes"
                    :aria-selected="index === this.activePanelIndex"
                    :aria-controls="'tabpanel-' + index"
                    :href="'#tabpanel-' + index"
                    @click="this.togglePanel($event, index)"
                >
                    <span :class="tabItem.title.classes"
                          v-bind="tabItem.title.attributes"
                          v-html="tabItem.title.content"
                    ></span>
                    <small v-if="tabItem.subtitle"
                           :class="tabItem.subtitle.classes"
                           v-bind="tabItem.subtitle.attributes"
                           v-html="tabItem.subtitle.content"
                    ></small>
                </a>
            </li>
        </ul>
        <div class="tabs__content" :data-color-theme="this.colorTheme">
            <div v-for="(content, index) in this.contents"
                 :key="index"
                 role="tabpanel"
                 :id="'tabpanel-' + index"
                 :class="content.classes"
                 :aria-labelledby="'tab-' + index"
                 :data-open="index === this.activePanelIndex"
                 v-html="content.content"
            ></div>
        </div>
    </div>
</template>

<style lang="css">
/** Sadly, Sass-style BEM nesting e.g., &__ prefix does not work in vanilla CSS */
.tabs {
    margin-bottom: var(--spacing-lg);

    .tabs__tab-list {
        margin: 0;
        padding: 0 !important;
        display: flex;

        .tabs__tab-list__item {
            display: block;
            margin-block: 0;

            .tabs__tab-list__item__title {
                .tabs__tab-list__item__title__main,
                .tabs__tab-list__item__title__subtitle {
                    display: block;
                }
            }

            [role="tab"] {
                text-align: match-parent;
                cursor: pointer;
                display: block;
                width: 100%;
                padding: var(--spacing-xs) var(--spacing-sm);
                text-decoration-color: transparent;
                color: inherit;

                &:focus {
                    text-decoration-color: currentColor;
                }

                &[aria-selected="true"],
                &:hover, &:focus, &:active {
                    background: rgb(255 255 255 / 0.25);
                }
            }
        }
    }

    .tabs__content {
        background: white;
        padding-block: 0;

        [role="tabpanel"] {
            height: 0;
            overflow: hidden;
            opacity: 0;

            &[data-open="true"] {
                height: auto;
                opacity: 1;
                padding: var(--spacing-md);
            }

            @media (prefers-reduced-motion) {
                transition: none;
            }
        }

        h2, h3 {
            color: var(--theme-color);
        }
    }

    &[data-orientation="vertical"] {
        container-type: inline-size;
        display: flex;
        flex-direction: row;

        .tabs__tab-list {
            margin: 0;
            width: 14rem;
            flex-basis: 14rem;
            min-width: 14rem;
            flex-direction: column;
            justify-content: flex-start;

            .tabs__tab-list__item {
                width: 100%;
            }
        }

        .tabs__content {
            width: auto;
            flex-basis: auto;
            flex-grow: 1;
        }
    }
}
</style>
