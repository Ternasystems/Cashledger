<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use TS_Configuration\Classes\AbstractCls;
use TS_Database\Enums\DBDriver;
use TS_Exception\Classes\DBException;

/**
 * A factory class responsible for creating PDO database connection objects.
 */
final class DBConnection extends AbstractCls
{
    /**
     * @throws DBException
     */
    public static function create(DBCredentials $credentials): PDO
    {
        $dsn = self::buildDsn($credentials);

        try {
            return new PDO(
                $dsn,
                $credentials->user,
                $credentials->password,
                [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new DBException(
                'connection_failed',
                [':message' => $e->getMessage()],
                (int)$e->getCode(),
                $e
            );
        }
    }

    private static function buildDsn(DBCredentials $credentials): string
    {
        $host = $credentials->host;
        $port = $credentials->port;
        $dbName = $credentials->dbName;

        return match ($credentials->driver) {
            DBDriver::Mysql => "mysql:host={$host};" . ($port ? "port={$port};" : "") . "dbname={$dbName}",
            DBDriver::Pgsql => "pgsql:host={$host};" . ($port ? "port={$port};" : "") . "dbname={$dbName}",
            DBDriver::Sqlsrv => "sqlsrv:Server={$host}" . ($port ? ",{$port}" : "") . ";Database={$dbName}",
            DBDriver::Oracle => "oci:dbname=//{$host}" . ($port ? ":{$port}" : "") . "/{$dbName}",
        };
    }
}

