<?php
namespace Doubleedesign\Comet\Core;

/**
 * ResponsivePanels component
 *
 * @package Doubleedesign\Comet\Core
 * @version 1.0.0
 * @description Display grouped panels of content as an Accordion or Tabs depending on available space.
 *              Uses Vue to determine which to show and only renders the HTML for the current view.
 */
#[AllowedTags([Tag::DIV])]
#[DefaultTag(Tag::DIV)]
class ResponsivePanels extends PanelGroupComponent {
    use Icon;

    /**
     * @var string $breakpoint
     * @description The container breakpoint at which to switch between accordion and tabs
     */
    protected string $breakpoint;

    /**
     * @var ?string $icon
     * @description Icon class name for the icon to use for the expand/collapse indicator in accordion mode
     */
    protected ?string $icon;

    /** @var array<ResponsivePanel> */
    protected array $innerComponents;

    public function __construct(array $attributes, array $innerComponents) {
        parent::__construct($attributes, $innerComponents, 'components.ResponsivePanels.responsive-panels');
        $this->breakpoint = $attributes['breakpoint'] ?? '768px';
        $this->set_icon_from_attrs($attributes, 'fa-plus');
    }

    public function render(): void {
        $blade = BladeService::getInstance();

        echo $blade->make($this->bladeFile, [
            'classes'    => $this->get_filtered_classes(),
            'attributes' => $this->get_html_attributes(),
            'breakpoint' => $this->breakpoint,
            'panels'     => $this->get_panels(),
            'icon'       => "$this->iconPrefix $this->icon"
        ])->render();
    }
}
