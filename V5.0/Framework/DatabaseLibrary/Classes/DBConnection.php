<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use TS_Database\Enums\DBDriver;
use TS_Exception\Classes\DBException;

/**
 * A factory for creating PDO database connection objects.
 * Supports multiple database drivers.
 */
final class DBConnection
{
    /**
     * Creates and returns a PDO database connection instance.
     *
     * @param DBCredentials $credentials The database credentials.
     * @return PDO The configured PDO instance.
     * @throws DBException if the connection fails.
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
                    PDO::ATTR_PERSISTENT => true, // Optional: for persistent connections
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new DBException(
                localizedMessages: [
                    'en' => "Database connection failed: " . $e->getMessage()
                ],
                code: (int)$e->getCode(),
                previous: $e
            );
        }
    }

    /**
     * Builds the DSN string based on the database driver.
     *
     * @param DBCredentials $credentials
     * @return string The formatted DSN string.
     */
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