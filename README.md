# Comet Responsive Panels

The standalone version of Responsive Panels from the [Comet Components library](https://www.cometcomponents.io).

## Installation

Install using Composer:

```powershell
composer require doubleedesign/comet-responsive-panels
```

Ensure your project loads dependencies using the autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

You will need to load the CSS and JS assets for the component into your project, however you usually do so. You will need:

- `/vendor/doubleedesign/comet-responsive-panels/dist/src/components/global.css`
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
        $rootDir = plugin_dir_url(__FILE__) . '..';
        wp_enqueue_style('comet-responsive-panels', $rootDir . '/vendor/doubleedesign/comet-responsive-panels/dist/src/components/global.css', [], '0.0.2');
        wp_enqueue_style('comet-responsive-panels', $rootDir . '/vendor/doubleedesign/comet-responsive-panels/dist/src/components/ResponsivePanels/responsive-panels.css', [], '0.0.2');
        wp_enqueue_script('comet-responsive-panels', $rootDir . '/vendor/doubleedesign/comet-responsive-panels/dist/src/components/ResponsivePanels/responsive-panels.js', [], '0.0.2', true);
    }

    public function script_type_module($tag, $handle, $src): mixed {
        if (str_starts_with($handle, 'comet-')) {
            $rootDir = plugin_dir_url(__FILE__) . '..';
            $basePath = $rootDir . '/vendor/doubleedesign/comet-responsive-panels/dist';
            $tag = '<script type="module" data-base-path=' . "$basePath" . ' src="' . esc_url($src) . '" id="' . $handle . '" ></script>';
        }

        return $tag;
    }
}
```
