<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use PDOStatement;
use TS_Exception\Classes\DBException;

/**
 * Provides a unified and simplified interface for all database interactions.
 * This class acts as a high-level wrapper around a PDO connection object,
 * simplifying common tasks like SELECT, INSERT, UPDATE, DELETE, and transactions.
 */
final class DBContext
{
    /**
     * The constructor receives a pre-configured PDO instance, following
     * the Dependency Injection pattern.
     *
     * @param PDO $pdo A connected and configured PDO instance.
     */
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Executes a SELECT query and returns all results as an array of associative arrays.
     *
     * @param string $sql The SQL query to execute.
     * @param array<string, mixed> $params The parameters to bind to the query.
     * @return array<int, array<string, mixed>>
     * @throws DBException if the query fails.
     */
    public function select(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new DBException('query_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }

    /**
     * Executes a SELECT query and returns a single row.
     *
     * @param string $sql The SQL query to execute.
     * @param array<string, mixed> $params The parameters to bind to the query.
     * @return array<string, mixed>|null An associative array for the single row, or null if no result.
     * @throws DBException
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $result = $this->select($sql, $params);
        return $result[0] ?? null;
    }

    /**
     * Executes an INSERT, UPDATE, or DELETE statement.
     *
     * @param string $sql The SQL statement to execute.
     * @param array<string, mixed> $params The parameters to bind to the statement.
     * @return int The number of affected rows.
     * @throws DBException if the execution fails.
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new DBException('query_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }

    /**
     * Prepares an SQL statement for execution, useful for manual iteration or complex binding.
     *
     * @param string $sql The SQL statement to prepare.
     * @return PDOStatement The prepared statement object.
     * @throws DBException if the preparation fails.
     */
    public function prepare(string $sql): PDOStatement
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new DBException('preparation_failed', [':reason' => $e->getMessage()], (int)$e->getCode(), $e);
        }
    }

    /**
     * Initiates a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commits the current database transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rolls back the current database transaction.
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     */
    public function getLastInsertId(?string $name = null): string|false
    {
        return $this->pdo->lastInsertId($name);
    }
}
