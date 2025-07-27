<?php
namespace Doubleedesign\Comet\Core;

trait LayoutOrientation {
    /**
     * @var Orientation|null $orientation
     * @description Orientation of the component content, if applicable
     */
    protected ?Orientation $orientation = Orientation::VERTICAL;

    /**
     * @param  array  $attributes
     * @param  Orientation|null  $default
     * @description Retrieves the relevant properties from the component $attributes array, validates them, and assigns them to the corresponding component instance field.
     */
    protected function set_orientation_from_attrs(array $attributes, ?Orientation $default = Orientation::VERTICAL): void {
        // In WordPress, some blocks have $attributes['theSetting'] and some have $attributes['layout']['theSetting'] so we need to account for both
        $orientation = $attributes['orientation'] ?? $attributes['layout']['orientation'] ?? null;
        $this->orientation = isset($orientation) ? Orientation::tryFrom($orientation) : $default;
    }
}
