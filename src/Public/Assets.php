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
        // Only enqueue if the tool was used on the page.
        if (empty(self::$used)) {
            return;
        }

        $ver = (string) ($this->config['version'] ?? '0.1.0');

        // For now, we’ll load from assets/src (no build step yet).
        // Later, switch to assets/dist when you add Vite.
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

        // Provide REST base + nonce (nonce optional for public endpoints, but good practice to include).
        wp_add_inline_script(
            'pettools-tool',
            'window.PetTools = window.PetTools || {}; window.PetTools.restBase = ' . wp_json_encode(rest_url('pet-tools/v1/')) . ';',
            'before'
        );
    }
}
