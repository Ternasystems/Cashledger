<?php

declare(strict_types=1);

namespace TS_Cache\Classes;

use TS_Cache\Interfaces\ICacheAdapter;
use TS_Configuration\Classes\AbstractCls;

/**
 * An in-memory cache adapter using a simple PHP array.
 *
 * NOTE: This cache is NOT persistent. Its lifetime is limited to a single
 * PHP request. It is useful for testing or for implementing patterns
 * like an Identity Map to avoid re-fetching data within one request.
 */
class ArrayCacheAdapter extends AbstractCls implements ICacheAdapter
{
    /** @var array<string, array{expires: int, value: mixed}> */
    private array $storage = [];

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!isset($this->storage[$key]))
            return false;

        // Check if the item has expired
        return time() < @unserialize($this->storage[$key]['expires']);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): mixed
    {
        if (!$this->has($key))
            return null;

        return @unserialize($this->storage[$key]['value']);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int $seconds): bool
    {
        $this->storage[$key] = [
            'expires' => time() + $seconds,
            'value' => serialize($value)
        ];
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        unset($this->storage[$key]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function flush(): bool
    {
        $this->storage = [];
        return true;
    }
}