<?php

declare(strict_types=1);

namespace TS_Configuration\Interfaces;

/**
 * Defines the contract for a log handler, which processes a log record.
 */
interface ILogHandler
{
    /**
     * Handles a log record.
     *
     * @param string $level The severity level of the log.
     * @param string $message The log message.
     * @param array<string, mixed> $context The contextual data.
     */
    public function log(string $level, string $message, array $context): void;
}
