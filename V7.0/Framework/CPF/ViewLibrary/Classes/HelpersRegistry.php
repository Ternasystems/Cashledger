<?php

declare(strict_types=1);

namespace TS_View\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\ViewException;

/**
 * A central registry for custom view helper functions.
 * This service allows for the dynamic addition of helpers that can be
 * accessed from within view templates.
 */
class HelpersRegistry extends AbstractCls
{
    /** @var array<string, callable> */
    private array $helpers = [];

    /**
     * Registers a new helper function.
     *
     * @param string $name The name of the helper (how it will be called).
     * @param callable $callback The closure or function to execute.
     * @throws ViewException
     */
    public function add(string $name, callable $callback): void
    {
        if (isset($this->helpers[$name])) {
            // Or log a warning, depending on desired strictness.
            throw new ViewException('helper_already_registered', [':name' => $name]);
        }
        $this->helpers[$name] = $callback;
    }

    /**
     * Checks if a helper is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->helpers[$name]);
    }

    /**
     * Calls a registered helper function.
     *
     * @param string $name The name of the helper to call.
     * @param array $arguments The arguments to pass to the helper.
     * @return mixed The result of the helper function.
     * @throws ViewException if the helper is not found.
     */
    public function call(string $name, array $arguments): mixed
    {
        if (!$this->has($name)) {
            throw new ViewException('helper_not_found', [':name' => $name]);
        }
        return $this->helpers[$name](...$arguments);
    }
}