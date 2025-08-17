<?php
namespace Doubleedesign\Comet\Core;

#[AllowedTags([Tag::DIV])]
#[DefaultTag(Tag::DIV)]
abstract class PanelGroupComponent extends UIComponent {
    use ColorTheme;
    use LayoutOrientation;

    /**
     * @var array
     * @description Panel data transformed for use by the relevant Vue component.
     */
    private array $panels = [];

    public function __construct(array $attributes, array $innerComponents, string $bladeFile) {
        parent::__construct($attributes, $innerComponents, $bladeFile);
        $this->set_color_theme_from_attrs($attributes, ThemeColor::PRIMARY);
        $this->set_orientation_from_attrs($attributes);
        $this->prepare_inner_components_for_vue();
    }

    private function prepare_inner_components_for_vue(): void {
        foreach ($this->innerComponents as $panel) {
            if (!$panel instanceof PanelComponent) {
                error_log('Accordion: Invalid inner component type found and ignored.');
            }

            $this->panels[] = [
                'summary' => $panel->get_summary(),
                'content' => $panel->get_content(),
            ];
        }
    }

    protected function get_panels(): array {
        return $this->panels;
    }

    protected function get_html_attributes(): array {
        $attributes = parent::get_html_attributes();

        if (isset($this->orientation)) {
            $attributes['data-orientation'] = $this->orientation->value;
        }

        if (isset($this->colorTheme)) {
            $attributes['data-color-theme'] = $this->colorTheme->value;
        }

        return $attributes;
    }

}
