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

    // Cache layer (object cache + transient fallback)
    $this->container->set('cache', function () {
        return new \PetTools\Infrastructure\Cache('pettools_');
    });

    // Domain layer (pure PHP, testable)
    $this->container->set('growth_model', function () {
        return new \PetTools\Domain\Calculator\GrowthModel();
    });

    $this->container->set('puppy_weight_calculator', function (Container $c) {
        return new \PetTools\Domain\Calculator\PuppyWeightCalculator(
            $c->get('growth_model')
        );
    });

    // REST endpoint
    $this->container->set('puppy_weight_endpoint', function (Container $c) {
        return new \PetTools\Rest\PuppyWeightEndpoint(
            $c->get('puppy_weight_calculator'),
            $c->get('cache')
        );
    });

    // Placeholder services (we’ll implement these next).
    $this->container->set('shortcodes', function (): object {
        return new class {
            public function register(): void
            {
                // Next: add_shortcode('pettools_puppy_weight', ...)
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
                // Next: wp_enqueue_script/style from /assets/dist
                // We'll make this conditional (only when shortcode exists).
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
