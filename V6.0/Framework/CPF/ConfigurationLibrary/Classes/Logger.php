<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use TS_Configuration\Enums\LogLevel;
use TS_Configuration\Interfaces\ILogger;
use TS_Configuration\Interfaces\ILogHandler;
use Stringable;

class Logger extends AbstractCls implements ILogger
{
    /** @var ILogHandler[] */
    private array $handlers = [];

    public function addHandler(ILogHandler $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY->value, $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT->value, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL->value, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR->value, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING->value, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE->value, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO->value, $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG->value, $message, $context);
    }

    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        foreach ($this->handlers as $handler) {
            $handler->log((string)$level, (string)$message, $context);
        }
    }
}
