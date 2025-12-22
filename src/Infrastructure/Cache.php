<?php
declare(strict_types=1);

namespace PetTools\Infrastructure;

final class Cache
{
    private string $prefix;

    public function __construct(string $prefix = 'pettools_')
    {
        $this->prefix = $prefix;
    }

    public function get(string $key): mixed
    {
        $key = $this->prefix . $key;

        // Prefer object cache if available
        if (function_exists('wp_cache_get')) {
            $cached = wp_cache_get($key, 'pettools');
            if ($cached !== false) {
                return $cached;
            }
        }

        // Fallback: transients
        if (function_exists('get_transient')) {
            $cached = get_transient($key);
            if ($cached !== false) {
                return $cached;
            }
        }

        return null;
    }

    public function set(string $key, mixed $value, int $ttlSeconds = 3600): void
    {
        $key = $this->prefix . $key;

        if (function_exists('wp_cache_set')) {
            wp_cache_set($key, $value, 'pettools', $ttlSeconds);
        }

        if (function_exists('set_transient')) {
            set_transient($key, $value, $ttlSeconds);
        }
    }
}
