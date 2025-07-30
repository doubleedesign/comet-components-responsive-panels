# Comet Responsive Panels

The standalone version of Responsive Panels from the [Comet Components library](https://www.cometcomponents.io).

## Installation

Install using Composer:

```powershell
composer require doubleedesign/comet-responsive-panels
```

> [!INFO]
> Like many libraries, this isn't 100% standalone - it uses some other libraries. There is one dependency, `doubleedesign/comet-components-launchpad`, which contains Comet Components Core's foundational classes and global CSS, and the dependencies for using Blade templates. This is so that if you use multiple standalone packages in your project, you don't end up with unnecessary duplication.

Ensure your project loads dependencies using the autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

You will need to load the CSS and JS assets for the component into your project, however you usually do so. You will need:

- `/vendor/doubleedesign/comet-components-launchpad/dist/src/components/global.css`
- `/vendor/doubleedesign/comet-responsive-panels/dist/src/components/responsive-panels.css`
- `/vendor/doubleedesign/comet-responsive-panels/dist/src/components/responsive-panels.js`

The JS file also needs to be specified as a module, and a base path given for the Vue loader to pick up. For example:

```html

<script type="module" data-base-path="/vendor/doubleedesign/comet-responsive-panels/dist" src="/vendor/doubleedesign/comet-responsive-panels/dist/src/components/responsive-panels.js"></script>
```

An example of how you might set up the client-side assets in a WordPress plugin is:

```php
<?php
namespace YourNamespace\PluginName;

class Frontend {
	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'load_frontend_assets']);
		add_filter('script_loader_tag', [$this, 'script_type_module'], 10, 3);
	}
	
	public function load_frontend_assets(): void {
		$depDir = '/wp-content/plugins/simple-document-portal/vendor/doubleedesign/comet-components-launchpad/dist/src';
		$rootDir = '/wp-content/plugins/simple-document-portal/vendor/doubleedesign/comet-responsive-panels/dist/src';
		$antiCacheVer = filemtime(WP_CONTENT_DIR . '/plugins/simple-document-portal/vendor/doubleedesign/comet-responsive-panels/dist/src/components/ResponsivePanels/responsive-panels.js');
	
		wp_enqueue_style('comet-global', $depDir . '/src/components/global.css', [], '0.0.2');
		wp_enqueue_style('comet-responsive-panels', $rootDir . '/components/ResponsivePanels/responsive-panels.css', [], $antiCacheVer);
		wp_enqueue_script('comet-responsive-panels', $rootDir . '/components/ResponsivePanels/responsive-panels.js', [], $antiCacheVer, true);
	}
	
	public function script_type_module($tag, $handle, $src): mixed {
		if(str_starts_with($handle, 'comet-')) {
			$rootDir = '/wp-content/plugins/simple-document-portal/vendor/doubleedesign/comet-responsive-panels/dist';
			$src = esc_url($src);
			$tag = "<script type=\"module\" data-base-path=\"$rootDir\" src=\"" . $src . "\" id=\"$handle\"></script>";
		}	
	
		return $tag;
	}
}
```

An example implementation can be seen in the [Simple Document Portal](https://github.com/doubleedesign/simple-document-portal) plugin.
