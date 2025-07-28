const isDev = window.location.hostname.endsWith('local') || window.location.hostname.endsWith('test');

export default isDev
	? import("./vue.esm-browser.js")
	: import("./vue.esm-browser.prod.js");
