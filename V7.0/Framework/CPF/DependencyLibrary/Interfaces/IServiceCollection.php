<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Interfaces;

/**
 * Defines the contract for a collection of service registrations.
 * Its sole purpose is to configure which services are available.
 */
interface IServiceCollection
{
    /**
     * Adds a transient service to the collection.
     * A new instance is created every time it is requested.
     *
     * @param string $id The abstract class or interface identifier.
     * @param string|callable $concrete The concrete class name or a factory closure.
     */
    public function addTransient(string $id, string|callable $concrete): void;

    /**
     * Adds a scoped service to the collection.
     * A single instance is created on the first request and reused for the
     * entire duration of that single HTTP request.
     *
     * @param string $id The abstract class or interface identifier.
     * @param string|callable $concrete The concrete class name or a factory closure.
     */
    public function addScoped(string $id, string|callable $concrete): void;

    /**
     * Adds a singleton service to the collection.
     * The service is instantiated once, and its state is persisted in a cache
     * to be shared across multiple HTTP requests.
     *
     * @param string $id The abstract class or interface identifier.
     * @param string|callable $concrete The concrete class name or a factory closure.
     * @param int $ttl The Time To Live in seconds for the cached singleton. 0 means cache forever.
     */
    public function addSingleton(string $id, string|callable $concrete, int $ttl = 0): void;
}