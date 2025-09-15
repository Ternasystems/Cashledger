<?php

declare(strict_types=1);

namespace TS_Database\Classes;

use PDO;
use PDOException;
use PDOStatement;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DBException;

/**
 * Provides a unified interface for database interactions and acts as a factory for the QueryBuilder.
 */
final class DBContext extends AbstractCls
{
    private PDO $pdo;

    /**
     * @throws DBException
     */
    public function __construct(DBCredentials $credentials)
    {
        $this->pdo = DBConnection::create($credentials);

        // Make the PDO connection available to all models that extend AbstractModel.
        // This should be done only once when the context is created.
        AbstractModel::setConnection($this->pdo);
    }

    /**
     * Creates a new QueryBuilder instance for a specific table.
     */
    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->pdo, $table);
    }

    /**
     * @throws DBException
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
     * @throws DBException
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $result = $this->select($sql, $params);
        return $result[0] ?? null;
    }

    /**
     * @throws DBException
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

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}

