<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use TS_DependencyInjection\Interfaces\IServiceCollection;
use TS_Exception\Classes\DIException; // A new, specific exception type we should add

/**
 * The main application class, acting as a powerful dependency injection container.
 * It handles service registration, resolution, and auto-wiring.
 */
final class Application implements IServiceCollection
{
    private array $definitions = [];
    private array $singletons = [];

    public function addTransient(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => false];
    }

    public function addScoped(string $id, string|callable $concrete): void
    {
        // In a traditional web request, Scoped is identical to Singleton.
        $this->addSingleton($id, $concrete);
    }

    public function addSingleton(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => true];
    }

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
                // Auto-wired singletons are a common feature. We can treat any auto-wired
                // concrete class as a singleton by default for simplicity.
                $this->singletons[$id] = $instance;
                return $instance;
            }
            throw new DIException(["en" => "Service not found: $id"]);
        }

        $definition = $this->definitions[$id];
        $concrete = $definition['concrete'];

        $instance = is_callable($concrete)
            ? $concrete($this) // It's a factory, so call it.
            : $this->resolve($concrete); // It's a class name, so resolve it.

        if ($definition['is_singleton']) {
            $this->singletons[$id] = $instance;
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    private function resolve(string $class): object
    {
        try {
            $reflector = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new DIException(['en' => "Failed to reflect class: $class"], previous: $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new DIException(['en' => "Class is not instantiable: $class"]);
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
                throw new DIException(['en' => "Cannot resolve primitive parameter '{$parameter->getName()}' in class $class."]);
            }
            $dependencies[] = $this->get($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}