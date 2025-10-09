<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use TS_Cache\Classes\CacheManager;
use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Enums\Lifetime;
use TS_DependencyInjection\Interfaces\IServiceCollection;

class ApplicationBuilder extends AbstractCls implements IServiceCollection
{
    /**
     * Implements the Builder pattern to configure and create the main Application container.
     * This is the primary API for service registration.
     */
    /** @var array<string, array{concrete: string|callable, lifetime: Lifetime, ttl: int}> */
    private array $definitions = [];
    private ?CacheManager $cache;

    /**
     * @param CacheManager|null $cache Optional cache manager for handling Singleton lifetimes.
     */
    public function __construct(?CacheManager $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function addTransient(string $id, callable|string $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'lifetime' => Lifetime::TRANSIENT, 'ttl' => 0];
    }

    /**
     * @inheritDoc
     */
    public function addScoped(string $id, callable|string $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'lifetime' => Lifetime::SCOPED, 'ttl' => 0];
    }

    /**
     * @inheritDoc
     */
    public function addSingleton(string $id, callable|string $concrete, int $ttl = 0): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'lifetime' => Lifetime::SINGLETON, 'ttl' => $ttl];
    }

    /**
     * Creates the final, configured Application container.
     *
     * @return Application An immutable, ready-to-use DI container.
     */
    public function build(): Application
    {
        return new Application($this->definitions, $this->cache);
    }
}