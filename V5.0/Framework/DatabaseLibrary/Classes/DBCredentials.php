<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use TS_Database\Enums\DBDriver;

/**
 * An immutable value object to hold database connection credentials.
 */
final class DBCredentials
{
    public function __construct(
        public readonly DBDriver $driver,
        public readonly string $host,
        public readonly string $dbName,
        public readonly string $user,
        public readonly string $password,
        public readonly int $port = 0
    ) {
    }
}