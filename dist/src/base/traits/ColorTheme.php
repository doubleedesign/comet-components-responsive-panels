<?php
namespace Doubleedesign\Comet\Core;

trait ColorTheme {
    /**
     * @var ?ThemeColor $colorTheme
     * @description Colour keyword for the fill or outline colour
     */
    protected ?ThemeColor $colorTheme;

    /**
     * @param  array  $attributes
     * @param  ?ThemeColor  $default
     * @description Retrieves the relevant properties from the component $attributes array, validates them, and assigns them to the corresponding component instance field.
     */
    protected function set_color_theme_from_attrs(array $attributes, ?ThemeColor $default = null): void {
        $this->colorTheme = isset($attributes['colorTheme'])
            ? ThemeColor::tryFrom($attributes['colorTheme'])
            : $default;
    }
}
