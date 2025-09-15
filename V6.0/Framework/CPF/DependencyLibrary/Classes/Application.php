<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DIException;

/**
 * The main application class, acting as a powerful, immutable dependency injection container.
 * Its sole responsibility is to resolve services based on the configuration
 * provided by the ApplicationBuilder.
 */
final class Application extends AbstractCls
{
    /** @var array<string, array{concrete: string|callable, is_singleton: bool}> Holds all service definitions. */
    private readonly array $definitions;

    /** @var array<string, mixed> Acts as a cache for singleton instances. */
    private array $singletons = [];

    /**
     * The constructor is internal. The container must be created via the ApplicationBuilder.
     * @internal
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Checks if a service is registered in the container.
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * Gets a service instance from the container.
     *
     * @template T
     * @param class-string<T> $id The identifier of the service to retrieve.
     * @return mixed The service instance.
     * @throws DIException|ReflectionException
     */
    public function get(string $id): mixed
    {
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }

        $definition = $this->definitions[$id] ?? ['concrete' => $id, 'is_singleton' => false];

        $concrete = $definition['concrete'];
        $isSingleton = $definition['is_singleton'];

        $instance = is_callable($concrete) ? $concrete($this) : $this->resolve($concrete);

        if ($isSingleton) {
            $this->singletons[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Uses reflection to automatically build an object and inject its dependencies.
     *
     * @param class-string $class The concrete class name to instantiate.
     * @return object The fully constructed object.
     * @throws DIException|ReflectionException
     */
    private function resolve(string $class): object
    {
        try {
            $reflector = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new DIException('failed_to_reflect', [':class' => $class], previous: $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new DIException('class_not_instantiable', [':class' => $class]);
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new DIException('unresolvable_parameter', [
                    ':param' => $parameter->getName(),
                    ':class' => $class,
                ]);
            }
            $dependencies[] = $this->get($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}

