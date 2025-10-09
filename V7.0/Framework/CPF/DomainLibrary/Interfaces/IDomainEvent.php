<?php

declare(strict_types=1);

namespace TS_Domain\Interfaces;

use DateTimeImmutable;

/**
 * A contract for an event that represents a significant change in the state
 * of the business domain.
 */
interface IDomainEvent extends IEvent
{
    /**
     * Returns the exact date and time the event occurred.
     */
    public function occurredOn(): DateTimeImmutable;
}