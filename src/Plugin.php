<?php
declare(strict_types=1);

namespace PetTools;

final class Plugin
{
    private Container $container;

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?? new Container();
    }

    /**
     * Entry point called from the plugin bootstrap file.
     */
    public function register(): void
    {
        $this->registerServices();
        $this->registerHooks();
    }

    /**
     * Register dependencies/services here (no WP hooks in this method).
     */
    private function registerServices(): void
    {
        // Store plugin config in container (useful for enqueuing, paths, versioning, etc.)
        $this->container->set('config', function (): array {
            return [
                'version' => defined('PETTOOLS_VERSION') ? PETTOOLS_VERSION : '0.1.0',
                'path'    => defined('PETTOOLS_PATH') ? PETTOOLS_PATH : plugin_dir_path(__DIR__ . '/../pet-tools-suite.php'),
                'url'     => defined('PETTOOLS_URL') ? PETTOOLS_URL : plugin_dir_url(__DIR__ . '/../pet-tools-suite.php'),
            ];
        });

        // Placeholder services (we’ll implement these later).
        // Keeping stubs now makes wiring obvious and “lead-level”.
        $this->container->set('shortcodes', function (): object {
            return new class {
                public function register(): void
                {
                    // Later: add_shortcode('pettools_puppy_weight', ...)
                }
            };
        });

        $this->container->set('rest', function (): object {
            return new class {
                public function register_routes(): void
                {
                    // Later: register_rest_route(...)
                }
            };
        });

        $this->container->set('assets', function (Container $c): object {
            $config = $c->get('config');

            return new class($config) {
                /** @var array<string,mixed> */
                private array $config;

                /** @param array<string,mixed> $config */
                public function __construct(array $config)
                {
                    $this->config = $config;
                }

                public function enqueue_public(): void
                {
                    // Later: wp_enqueue_script/style from /assets/dist
                    // Intentionally empty for now.
                }
            };
        });
    }

    /**
     * All WordPress hooks are registered in one place (reviewable + maintainable).
     */
    private function registerHooks(): void
    {
        // Register shortcodes / blocks
        add_action('init', function (): void {
            $this->container->get('shortcodes')->register();
        });

        // Register REST routes
        add_action('rest_api_init', function (): void {
            $this->container->get('rest')->register_routes();
        });

        // Enqueue public assets (later we’ll conditionally enqueue only when tool is used)
        add_action('wp_enqueue_scripts', function (): void {
            $this->container->get('assets')->enqueue_public();
        });
    }
}
