<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_DependencyInjection\Interfaces\IServiceCollection;

/**
 * Implements the Builder pattern to configure and create the main Application container.
 * This is the primary API for service registration.
 */
final class ApplicationBuilder extends AbstractCls implements IServiceCollection
{
    /** @var array<string, array{concrete: string|callable, is_singleton: bool}> */
    private array $definitions = [];

    public function __construct()
    {
    }

    public function addTransient(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => false];
    }

    public function addScoped(string $id, string|callable $concrete): void
    {
        // In a typical PHP request, Scoped is identical to Singleton.
        $this->addSingleton($id, $concrete);
    }

    public function addSingleton(string $id, string|callable $concrete): void
    {
        $this->definitions[$id] = ['concrete' => $concrete, 'is_singleton' => true];
    }

    /**
     * Creates the final, configured Application container.
     *
     * @return Application An immutable, ready-to-use DI container.
     */
    public function build(): Application
    {
        return new Application($this->definitions);
    }
}

