<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use TS_Cache\Classes\CacheManager;
use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Enums\Lifetime;
use TS_Exception\Classes\DIException;

/**
 * The main application class, acting as a powerful, immutable dependency injection container.
 * Its sole responsibility is to resolve services based on the configuration
 * provided by the ApplicationBuilder.
 */
class Application extends AbstractCls
{
    private readonly array $definitions;
    private readonly ?CacheManager $cache;
    private array $scopedInstances = [];

    /**
     * @internal
     */
    public function __construct(array $definitions, ?CacheManager $cache = null)
    {
        $this->definitions = $definitions;
        $this->cache = $cache;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * @throws DIException|ReflectionException
     */
    public function get(string $id): mixed
    {
        // For Scoped and Singleton, first check the per-request cache.
        if (isset($this->scopedInstances[$id])) {
            return $this->scopedInstances[$id];
        }

        $definition = $this->definitions[$id] ?? ['concrete' => $id, 'lifetime' => Lifetime::TRANSIENT, 'ttl' => 0];
        $lifetime = $definition['lifetime'];
        $cacheKey = '';

        if ($lifetime === Lifetime::SINGLETON && $this->cache) {
            $cacheKey = 'di_singleton_' . str_replace('\\', '_', $id);
            if ($this->cache->has($cacheKey)) {
                $instance = $this->cache->get($cacheKey);
                $this->scopedInstances[$id] = $instance; // Cache for this request
                return $instance;
            }
        }

        // If not a cached singleton, or if cache misses, resolve the instance.
        $instance = $this->buildInstance($definition);

        if ($lifetime === Lifetime::SCOPED) {
            $this->scopedInstances[$id] = $instance;
        }

        if ($lifetime === Lifetime::SINGLETON && $this->cache) {
            $this->cache->set($cacheKey, $instance, $definition['ttl']);
            $this->scopedInstances[$id] = $instance; // Also cache for this request
        }

        return $instance;
    }

    /**
     * @throws DIException|ReflectionException
     */
    private function buildInstance(array $definition): mixed
    {
        $concrete = $definition['concrete'];
        return is_callable($concrete) ? $concrete($this) : $this->resolve($concrete);
    }

    /**
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