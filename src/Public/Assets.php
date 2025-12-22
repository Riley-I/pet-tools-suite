<?php
declare(strict_types=1);

namespace PetTools\Public;

final class Assets
{
    /** @var array<string,bool> */
    private static array $used = [];

    /** @var array<string,mixed> */
    private array $config;

    /** @param array<string,mixed> $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function markUsed(string $tool): void
    {
        self::$used[$tool] = true;
    }

   public function enqueuePublic(): void
{
    // Enqueue only on pages that contain the shortcode.
    $post = get_post();
    if (!$post || empty($post->post_content)) {
        return;
    }

    if (!has_shortcode($post->post_content, 'pettools_puppy_weight')) {
        return;
    }

    $ver = (string) ($this->config['version'] ?? '0.1.0');

    wp_enqueue_style(
        'pettools-tool',
        $this->config['url'] . 'assets/src/css/tool.css',
        [],
        $ver
    );

    wp_enqueue_script(
        'pettools-tool',
        $this->config['url'] . 'assets/src/js/tool.js',
        [],
        $ver,
        true
    );

    wp_add_inline_script(
        'pettools-tool',
        'window.PetTools = window.PetTools || {}; window.PetTools.restBase = ' . wp_json_encode(rest_url('pet-tools/v1/')) . ';',
        'before'
    );
}

}
