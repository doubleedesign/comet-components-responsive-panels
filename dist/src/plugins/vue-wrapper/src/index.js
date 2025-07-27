import VueLoader from './vue-loader.js';
const Vue = await VueLoader;

export const vueSfcLoaderOptions = {
	moduleCache: {vue: Vue},
	pathResolve({refPath, relPath}, options) {
		// Fix import relative path resolution
		// e.g., when coming from Storybook imports of Accordion and Tabs within ResponsivePanels would break without this
		// Source: https://github.com/FranckFreiburger/vue3-sfc-loader/blob/main/docs/examples.md#use-remote-components

		if (relPath === ".") return refPath; // self

		if (relPath[0] !== "." && relPath[0] !== "/") return relPath; // relPath is a module name

		return String(new URL(relPath, refPath === undefined ? window.location : refPath));
	},
	getFile: async (url) => {
		const res = await fetch(url);
		if (!res.ok) {
			throw Object.assign(new Error(res.statusText + " " + url), {res});
		}

		return {
			getContentData: () => {
				return res.text().then((content) => {
					// Filter out the <style> tags from the component as they need to be processed separately
					const dom = new DOMParser().parseFromString(content, "text/html");

					return Array.from(dom.head.children)
						.filter((element) => element.tagName !== "STYLE")
						.map((element) => element.outerHTML)
						.join("\n");
				});
			}
		};
	},
	addStyle: async (fileUrl) => {
		const res = await fetch(fileUrl);

		const dom = new DOMParser().parseFromString(await res.text(), "text/html");
		const css = Array.from(dom.head.children).find((element) => element.tagName === "STYLE");
		if (css?.textContent) {
			const style = document.createElement("style");
			style.setAttribute("data-vue-component", fileUrl.split("/").pop());
			style.type = "text/css";
			style.textContent = css.textContent;
			document.body.appendChild(style);
		}
	},
	async handleModule(type, getContentData, path, options) {
		if (type === ".vue") {
			await options.addStyle(path);
		}
	}
};

export const BASE_PATH = (function () {
	// NOTE: If we are loading from an implementation, the <script> tag for dist.js needs to have the data-base-path attribute set to
	// the path to the Core package, e.g./wp-content/plugins/comet-plugin/vendor/doubleedesign/comet-components-core
	// The below finds it there.
	const scripts = document.getElementsByTagName("script");
	for (let i = 0; i < scripts.length; i++) {
		if (scripts[i].hasAttribute("data-base-path")) {
			return scripts[i].getAttribute("data-base-path");
		}
	}

	// For individual asset loading:
	// In a local dev environment, default to the core package source directory
	if (window.location.hostname === "comet-components.test" || window.location.hostname === "localhost") {
		return "/packages/core/";
	}
	// For Storybook, we also need to include the project site domain
	if (window.location.hostname === "storybook.comet-components.test") {
		return "https://comet-components.test/packages/core/";
	}
	if (window.location.hostname === "storybook.cometcomponents.io" || window.location.hostname === "cometcomponents.io") {
		return "https://cometcomponents.io/packages/core/";
	}

	// Otherwise, assume vendor directory path
	return "/vendor/doubleedesign/comet-components-core/packages/core/";
})();
