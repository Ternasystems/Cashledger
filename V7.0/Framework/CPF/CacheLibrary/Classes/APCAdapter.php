<?php

declare(strict_types=1);

namespace TS_Cache\Classes;

use TS_Cache\Interfaces\ICacheAdapter;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\CacheException;

/**
 * An APCu-based implementation of the cache adapter.
 * This provides a very high-performance, in-memory cache for a single server.
 * NOTE: Requires the 'apcu' PHP extension.
 */
class APCAdapter extends AbstractCls implements ICacheAdapter
{
    /**
     * @throws CacheException
     */
    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new CacheException('apcu_extension_not_loaded');
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return apcu_exists($key);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): mixed
    {
        return apcu_fetch($key, $success) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int $seconds): bool
    {
        // TTL (Time To Live) of 0 means cache forever.
        $ttl = max($seconds, 0);
        return apcu_store($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return apcu_delete($key);
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        // This clears the entire user cache. Use with caution.
        return apcu_clear_cache();
    }
}