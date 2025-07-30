<?php
namespace Doubleedesign\Comet\Core;

// Note: This component's tag changes responsively,
// determined by the Vue component that renders within ResponsivePanels
#[AllowedTags([Tag::DIV, Tag::DETAILS])]
#[DefaultTag(Tag::DIV)]
class ResponsivePanel extends PanelComponent {

    public function __construct(array $attributes, array $innerComponents) {
        parent::__construct($attributes, $innerComponents, 'components.ResponsivePanels.ResponsivePanel.responsive-panel');
        $this->context = 'responsive-panel';
    }

    protected function get_bem_name(): ?string {
        return 'responsive-panel__content';
    }
}
