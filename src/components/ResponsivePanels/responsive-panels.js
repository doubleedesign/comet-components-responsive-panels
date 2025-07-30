import VueLoader from '../../plugins/vue-wrapper/src/vue-loader.js';
const Vue = await VueLoader;
import { loadModule } from  '../../plugins/vue-wrapper/src/vue3-sfc-loader.esm.js';
import { vueSfcLoaderOptions, BASE_PATH } from '../../plugins/vue-wrapper/src/index.js';

// Run on initial page load (works on the front-end)
init();

// Run on event trigger (makes it work for ACF blocks in the WP block editor where code is set up to trigger it appropriately)
window.addEventListener('ReloadVueResponsivePanels', (e) => {
	init();
});

export function init() {
	const instances = document.querySelectorAll('[data-vue-component="responsive-panels"]');
	if (instances.length > 0) {
		instances.forEach((instance, index) => {
			// Add a unique attribute to use as the mount point
			// (can't use ID because that could be set to a custom value by consumers)
			instance.setAttribute('data-responsive-panels-instance', `responsive-panels-${index}`);

			Vue.createApp({
				components: {
					ResponsivePanels: Vue.defineAsyncComponent(() => {
						return loadModule(`${BASE_PATH}/src/components/ResponsivePanels/responsive-panels.vue`, vueSfcLoaderOptions);
					}),
				}
			}).mount(`[data-responsive-panels-instance="responsive-panels-${index}"]`);
		});
	}
}
