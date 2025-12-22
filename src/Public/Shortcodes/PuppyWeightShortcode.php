<?php
declare(strict_types=1);

namespace PetTools\Public\Shortcodes;

use PetTools\Public\Assets;

final class PuppyWeightShortcode
{
    private Assets $assets;

    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    public function register(): void
    {
        add_shortcode('pettools_puppy_weight', [$this, 'render']);
    }

    public function render(array $atts = []): string
    {
        // Mark that the tool is used on this request so assets can load conditionally.
        $this->assets->markUsed('puppy_weight');

        // Tool container (JS enhances it)
        return '<div class="pettools-tool" data-pettools="puppy-weight"></div>';
    }
}
