const isDev = window.location.hostname.includes('local');

export default isDev
	? import("./vue.esm-browser.js")
	: import("./vue.esm-browser.prod.js");
