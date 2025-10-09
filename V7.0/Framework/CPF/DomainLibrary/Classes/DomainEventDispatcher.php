<?php

declare(strict_types=1);

namespace TS_Domain\Classes;

use Closure;
use TS_Configuration\Classes\AbstractCls;
use TS_Domain\Interfaces\IDomainEvent;

/**
 * A central dispatcher for Domain Events, based on the Observer pattern.
 * This should be registered as a singleton in the DI container.
 */
class DomainEventDispatcher extends AbstractCls
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    /**
     * Registers a listener to be called when a specific domain event is dispatched.
     *
     * @param class-string<IDomainEvent> $eventClass The class name of the event to listen for.
     * @param Closure $listener The closure or callable to execute.
     */
    public function listen(string $eventClass, Closure $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Dispatches a domain event, notifying all registered listeners.
     *
     * @param IDomainEvent ...$events The event object(s) to dispatch.
     */
    public function dispatch(IDomainEvent ...$events): void
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