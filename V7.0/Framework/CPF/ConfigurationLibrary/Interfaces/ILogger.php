<?php

declare(strict_types=1);

namespace TS_Configuration\Interfaces;

use Stringable;
use TS_Configuration\Enums\LogLevel;

/**
 * Describes a logger instance.
 * Based on the PSR-3 Logger Interface standard.
 */
interface ILogger
{
    public function emergency(string|Stringable $message, array $context = []): void;
    public function alert(string|Stringable $message, array $context = []): void;
    public function critical(string|Stringable $message, array $context = []): void;
    public function error(string|Stringable $message, array $context = []): void;
    public function warning(string|Stringable $message, array $context = []): void;
    public function notice(string|Stringable $message, array $context = []): void;
    public function info(string|Stringable $message, array $context = []): void;
    public function debug(string|Stringable $message, array $context = []): void;
    public function log(LogLevel $level, string|Stringable $message, array $context = []): void;
}
