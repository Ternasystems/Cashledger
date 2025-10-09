<?php

declare(strict_types=1);

namespace TS_Cache\Interfaces;

/**
 * Defines the contract for a cache storage adapter.
 *
 * This allows for different caching backends (files, Redis, etc.)
 * to be used interchangeably.
 */
interface ICacheAdapter
{
    /**
     * Checks if an item exists in the cache and has not expired.
     *
     * @param string $key The unique key for the cache item.
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Retrieves an item from the cache.
     *
     * @param string $key The unique key for the cache item.
     * @return mixed The cached data, or null if not found or expired.
     */
    public function get(string $key): mixed;

    /**
     * Stores an item in the cache for a specified duration.
     *
     * @param string $key The unique key for the cache item.
     * @param mixed $value The data to be cached. Must be serializable.
     * @param int $seconds The number of seconds the item should be cached for.
     * @return bool True on success, false on failure.
     */
    public function set(string $key, mixed $value, int $seconds): bool;

    /**
     * Deletes an item from the cache.
     *
     * @param string $key The unique key for the cache item.
     * @return bool True on success, false on failure.
     */
    public function delete(string $key): bool;

    /**
     * Clears the entire cache.
     * Be careful with this operation in production environments.
     *
     * @return bool True on success, false on failure.
     */
    public function flush(): bool;
}