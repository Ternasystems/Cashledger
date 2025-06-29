<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Interfaces;

/**
 * Defines the contract for a collection of service registrations
 * for a dependency injection container.
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
     * A single instance is created per scope (typically per HTTP request).
     *
     * @param string $id The abstract class or interface identifier.
     * @param string|callable $concrete The concrete class name or a factory closure.
     */
    public function addScoped(string $id, string|callable $concrete): void;

    /**
     * Adds a singleton service to the collection.
     * A single instance is created on first request and reused thereafter.
     *
     * @param string $id The abstract class or interface identifier.
     * @param string|callable $concrete The concrete class name or a factory closure.
     */
    public function addSingleton(string $id, string|callable $concrete): void;

    /**
     * Gets a service instance from the container.
     *
     * @template T
     * @param class-string<T> $id The identifier of the service to retrieve.
     * @return T The service instance.
     */
    public function get(string $id): mixed;

    /**
     * Checks if a service is registered in the container.
     *
     * @param string $id The identifier of the service.
     * @return bool
     */
    public function has(string $id): bool;
}