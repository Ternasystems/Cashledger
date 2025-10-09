<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use Closure;
use TS_Configuration\Classes\AbstractCls;
use TS_Domain\Interfaces\ISystemEvent;

/**
 * A central dispatcher for System Events, based on the Observer pattern.
 * This should be registered as a singleton in the DI container.
 */
class SystemEventDispatcher extends AbstractCls
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    /**
     * Registers a listener to be called when a specific system event is dispatched.
     *
     * @param class-string<ISystemEvent> $eventClass The class name of the event to listen for.
     * @param Closure $listener The closure or callable to execute.
     */
    public function listen(string $eventClass, Closure $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Dispatches a system event, notifying all registered listeners.
     *
     * @param ISystemEvent ...$events The event object(s) to dispatch.
     */
    public function dispatch(ISystemEvent ...$events): void
    {
        foreach ($events as $event) {
            $eventClass = get_class($event);
            if (!empty($this->listeners[$eventClass])) {
                foreach ($this->listeners[$eventClass] as $listener) {
                    $listener($event);
                }
            }
        }
    }
}