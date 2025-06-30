<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use TS_DependencyInjection\Interfaces\IServiceCollection;
use TS_Exception\Classes\DIException;

/**
 * The main application class, acting as a powerful dependency injection container.
 * It handles service registration, resolution, and auto-wiring of dependencies.
 */
final class Application implements IServiceCollection
{
    /** @var array<string, array{concrete: string|callable, is_singleton: bool}> Holds all service definitions. */
    private array $definitions = [];

    /** @var array<string, mixed> Acts as a cache for singleton instances. */
    private array $singletons = [];

    /** {@inheritdoc} */
    public function addTransient(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => false];
    }

    /** {@inheritdoc} */
    public function addScoped(string $id, string|callable $concrete): void
    {
        // In a traditional PHP web request, Scoped is identical to Singleton.
        $this->addSingleton($id, $concrete);
    }

    /** {@inheritdoc} */
    public function addSingleton(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => true];
    }

    /** {@inheritdoc}
     * @throws DIException
     */
    public function get(string $id): mixed
    {
        // If a singleton instance already exists, return it immediately.
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }

        if (!isset($this->definitions[$id])) {
            // If not registered, try to "auto-wire" it if it's a concrete class.
            if (class_exists($id)) {
                $instance = $this->resolve($id);
                // Treat any auto-wired concrete class as a singleton by default.
                $this->singletons[$id] = $instance;
                return $instance;
            }
            throw new DIException('service_not_found', [':id' => $id]);
        }

        $definition = $this->definitions[$id];
        $concrete = $definition['concrete'];

        $instance = is_callable($concrete)
            ? $concrete($this) // It's a factory, so call it, passing the container itself.
            : $this->resolve($concrete); // It's a class name, so resolve its dependencies.

        if ($definition['is_singleton']) {
            $this->singletons[$id] = $instance;
        }

        return $instance;
    }

    /** {@inheritdoc} */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * Creates an instance of a class by recursively resolving its constructor dependencies.
     * @template T of object
     * @param class-string<T> $class
     * @return T
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
            return new $class(); // No constructor, no dependencies to inject.
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                // The container cannot resolve built-in types (string, int, etc.)
                // as it doesn't know what value to provide.
                throw new DIException('unresolvable_parameter', [
                    ':param' => $parameter->getName(),
                    ':class' => $class,
                ]);
            }
            // Recursively resolve the dependency by calling get() on the container.
            $dependencies[] = $this->get($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
