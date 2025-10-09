<?php

declare(strict_types=1);

namespace TS_DependencyInjection\Enums;

/**
 * Defines the lifetime scopes for services in the DI container.
 */
enum Lifetime
{
    /** A new instance is created on every request. */
    case TRANSIENT;

    /** A single instance is shared for the duration of one HTTP request. */
    case SCOPED;

    /** The instance's state is persisted and shared across multiple HTTP requests via a cache. */
    case SINGLETON;
}
