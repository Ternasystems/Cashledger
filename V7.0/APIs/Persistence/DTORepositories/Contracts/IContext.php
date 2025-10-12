<?php

namespace API_DTORepositories_Contract;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use PDOStatement;

/**
 * Defines the contract for a data context.
 * Any context class that interacts with a repository must implement this interface.
 * This ensures the repository layer is decoupled from any specific context implementation.
 */
interface IContext
{
    /**
     * Selects all records for a given entity type.
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @return array The raw associative array of data from the database.
     */
    public function SelectAll(string $entityName, ?array $whereClause = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Selects a single record by its ID.
     * @param string $Id The ID of the record to fetch.
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @return array|null The raw associative array for the record, or null if not found.
     */
    public function SelectById(string $Id, string $entityName): ?array;

    /**
     * Executes an INSERT stored procedure.
     * @param string $entityName The FQCN of the entity model.
     * @param array|null $args The arguments for the stored procedure.
     */
    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void;

    /**
     * Executes an UPDATE stored procedure.
     * @param string $entityName The FQCN of the entity model.
     * @param array|null $args The arguments for the stored procedure.
     */
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void;

    /**
     * Executes a DELETE stored procedure.
     * @param string $entityName The FQCN of the entity model.
     * @param array|null $args The arguments for the stored procedure.
     */
    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void;

    /**
     * Executes a DISABLE stored procedure (soft delete).
     * @param string $entityName The FQCN of the entity model.
     * @param array|null $args The arguments for the stored procedure.
     */
    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void;

    /**
     * Maps a raw data array to a single DTO object.
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @param array $data The raw associative array from the database.
     * @return DTOBase|null The hydrated DTO object.
     */
    public function Mapping(string $entityName, array $data): ?DTOBase;

    /**
     * Maps a raw data array to a strongly-typed collection.
     * @param string $collectionName The short name of the collection (e.g., 'countrycollection').
     * @param array $data The raw array of database records.
     * @return Collectable|null The hydrated, queryable collection.
     */
    public function MappingCollection(string $collectionName, array $data): ?Collectable;

    // --- Generic Query Execution Methods ---

    public function ExecuteSelectAll(string $sql, array $params = []): array;
    public function ExecuteSelectOne(string $sql, array $params = []): ?array;
    public function ExecuteCommand(string $sql, array $params = []): int;

    /**
     * Prepares a raw SQL statement for manual execution, useful for complex scenarios
     * like stored procedures with OUT parameters.
     * @param string $sql The raw SQL statement.
     * @return PDOStatement
     */
    public function Prepare(string $sql): PDOStatement;

    /**
     * Begins a database transaction.
     * @return bool True on success, false on failure.
     */
    public function beginTransaction(): bool;

    /**
     * Commits the current database transaction.
     * @return bool True on success, false on failure.
     */
    public function commit(): bool;

    /**
     * Rolls back the current database transaction.
     * @return bool True on success, false on failure.
     */
    public function rollBack(): bool;
}