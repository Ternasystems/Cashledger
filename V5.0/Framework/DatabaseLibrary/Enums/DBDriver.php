<?php

declare(strict_types=1);

namespace TS_Database\Enums;

/**
 * Defines the supported database drivers.
 */
enum DBDriver: string
{
    case Mysql = 'mysql';
    case Pgsql = 'pgsql';
    case Sqlsrv = 'sqlsrv';
    case Oracle = 'oci';
}