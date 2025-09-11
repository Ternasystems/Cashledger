<?php

declare(strict_types=1);

namespace TS_Configuration\Enums;

/**
 * Describes log levels. Based on the PSR-3 standard.
 */
enum LogLevel: string
{
    case EMERGENCY = 'EMERGENCY';
    case ALERT     = 'ALERT';
    case CRITICAL  = 'CRITICAL';
    case ERROR     = 'ERROR';
    case WARNING   = 'WARNING';
    case NOTICE    = 'NOTICE';
    case INFO      = 'INFO';
    case DEBUG     = 'DEBUG';
}
