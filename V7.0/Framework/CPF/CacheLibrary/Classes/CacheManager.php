<?php

declare(strict_types=1);

namespace TS_Cache\Classes;

use Closure;
use TS_Cache\Interfaces\ICacheAdapter;
use TS_Configuration\Classes\AbstractCls;

class CacheManager extends AbstractCls
{
    public function __construct(private readonly ICacheAdapter $adapter)
    {
    }

    public function has(string $key): bool
    {
        return $this->adapter->has($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->adapter->get($key) ?? $default;
    }

    public function set(string $key, mixed $value, int $seconds): bool
    {
        return $this->adapter->set($key, $value, $seconds);
    }

    public function delete(string $key): bool
    {
        return $this->adapter->delete($key);
    }

    public function flush(): bool
    {
        return $this->adapter->flush();
    }

    /**
     * A convenience method to get an item from the cache, or execute a closure
     * and store the result if the item is not in the cache.
     *
     * @param string $key The cache key.
     * @param int $seconds How long to cache the result for.
     * @param Closure $callback The function to execute to get the fresh data.
     * @return mixed
     */
    public function cache(string $key, int $seconds, Closure $callback): mixed
    {
        if ($this->has($key))
            return $this->get($key);

        $value = $callback();

        $this->set($key, $value, $seconds);

        return $value;
    }
}