<?php

declare(strict_types=1);

namespace TS_Domain\Interfaces;

/**
 * A contract for an event that represents a low-level framework or system action,
 * completely independent of business logic.
 */
interface ISystemEvent extends IEvent
{
}