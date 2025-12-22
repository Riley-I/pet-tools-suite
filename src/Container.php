<?php
declare(strict_types=1);

namespace PetTools;

/**
 * Tiny DI container (singleton services).
 * - set($id, $factory)
 * - get($id) => shared instance
 */
final class Container
{
    /** @var array<string, callable(self):mixed> */
    private array $factories = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /**
     * @param string $id
     * @param callable(self):mixed $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
        // Reset any existing instance so re-registering is deterministic.
        unset($this->instances[$id]);
    }

    /**
     * @template T
     * @param string $id
     * @return T
     */
    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->factories[$id])) {
            throw new \RuntimeException(sprintf('Service not found in container: "%s"', $id));
        }

        $this->instances[$id] = ($this->factories[$id])($this);

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}
