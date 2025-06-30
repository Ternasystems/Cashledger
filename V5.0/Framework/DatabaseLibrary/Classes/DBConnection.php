<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use TS_Database\Enums\DBDriver;
use TS_Exception\Classes\DBException;

/**
 * A factory class responsible for creating PDO database connection objects.
 * Its sole purpose is to build the correct DSN string for various database
 * drivers and establish a connection, throwing a specific exception on failure.
 */
final class DBConnection
{
    /**
     * Creates and returns a PDO database connection instance.
     *
     * @param DBCredentials $credentials The object containing all connection parameters.
     * @return PDO The configured PDO instance.
     * @throws DBException if the connection fails for any reason.
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
            // Throw our custom exception using a key and passing the original message as a placeholder.
            throw new DBException(
                'connection_failed',
                [':message' => $e->getMessage()],
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * Builds the appropriate Data Source Name (DSN) string based on the database driver.
     *
     * @param DBCredentials $credentials The credentials object.
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
