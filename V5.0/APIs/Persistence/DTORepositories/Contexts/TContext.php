<?php

namespace API_DTORepositories_Context;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use Exception;
use PDOStatement;
use TS_Exception\Classes\DBException;

/**
 * A reusable trait that provides the core data mapping and database interaction
 * logic for any context class in the persistence layer.
 *
 * It assumes that any class using it will have the following properties defined:
 * - private DBContext $dbContext;
 * - protected array $entityMap;
 * - protected array $propertyMap;
 */
trait TContext
{
    /**
     * Dynamically generates the stored procedure call string based on the calling method's name.
     *
     * @param string $entityName The name of the entity class (e.g., Country::class).
     * @param array|null $args The arguments for the procedure.
     * @return string The formatted CALL statement.
     */
    protected function SetStatement(string $entityName = DTOBase::class, ?array $args = null): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerFunction = $backtrace[1]['function']; // e.g., "Insert", "Update"

        $classNameParts = explode('\\', $entityName);
        $simpleClassName = end($classNameParts);

        $procName = 'p_' . $callerFunction . $simpleClassName;
        $placeholders = !is_null($args) ? implode(', ', array_fill(0, count($args), '?')) : '';

        return sprintf('CALL "%s"(%s)', $procName, $placeholders);
    }

    /**
     * Selects all records for a given entity type.
     *
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @return array The raw associative array of data from the database.
     * @throws Exception if the entity name is invalid.
     */
    public function SelectAll(string $entityName): array
    {
        if (!property_exists($this, $entityName)) {
            throw new Exception("Property mapping for '$entityName' does not exist in this context.");
        }

        $tableName = $this->{$entityName};
        $sql = sprintf('SELECT * FROM "%s" WHERE "IsActive" IS NULL', $tableName);

        return $this->dbContext->select($sql);
    }

    /**
     * Selects a single record by its ID.
     *
     * @param string $Id The ID of the record to fetch.
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @return array|null The raw associative array for the record, or null if not found.
     * @throws Exception if the entity name is invalid.
     */
    public function SelectById(string $Id, string $entityName): ?array
    {
        if (!property_exists($this, $entityName)) {
            throw new Exception("Property mapping for '$entityName' does not exist in this context.");
        }

        $tableName = $this->{$entityName};
        $sql = sprintf('SELECT * FROM "%s" WHERE "ID" = ? AND "IsActive" IS NULL', $tableName);

        return $this->dbContext->selectOne($sql, [$Id]);
    }

    /**
     * @throws DBException
     */
    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    /**
     * @throws DBException
     */
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    /**
     * @throws DBException
     */
    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    /**
     * @throws DBException
     */
    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    // --- Generic Query Execution Wrappers ---

    /**
     * @throws DBException
     */
    public function ExecuteSelectAll(string $sql, array $params = []): array
    {
        return $this->dbContext->select($sql, $params);
    }

    /**
     * @throws DBException
     */
    public function ExecuteSelectOne(string $sql, array $params = []): ?array
    {
        return $this->dbContext->selectOne($sql, $params);
    }

    /**
     * @throws DBException
     */
    public function ExecuteCommand(string $sql, array $params = []): int
    {
        return $this->dbContext->execute($sql, $params);
    }

    /**
     * @throws DBException
     */
    public function Prepare(string $sql): PDOStatement
    {
        return $this->dbContext->prepare($sql);
    }

    /**
     * Maps a raw data array to a single DTO object.
     *
     * @param string $entityName The short name of the entity (e.g., 'country').
     * @param array $data The raw associative array from the database.
     * @return DTOBase|null The hydrated DTO object.
     * @throws Exception
     */
    public function Mapping(string $entityName, array $data): ?DTOBase
    {
        if (!isset($this->entityMap[$entityName])) {
            throw new Exception('Not a valid entity name: ' . $entityName);
        }

        $entityClass = $this->entityMap[$entityName];
        if (!class_exists($entityClass)) {
            throw new Exception('Not a valid class: ' . $entityClass);
        }

        $entity = new $entityClass();
        foreach ($data as $key => $value) {
            $propertyName = $this->propertyMap[$key] ?? $key;
            if (property_exists($entity, $propertyName)) {
                $entity->{$propertyName} = $value;
            }
        }
        return $entity;
    }

    /**
     * Maps a raw data array to a strongly-typed collection.
     *
     * @param string $collectionName The short name of the collection (e.g., 'countrycollection').
     * @param array $data The raw array of database records.
     * @return Collectable|null The hydrated, queryable collection.
     * @throws Exception
     */
    public function MappingCollection(string $collectionName, array $data): ?Collectable
    {
        if (!isset($this->entityMap[$collectionName])) {
            throw new Exception('Not a valid collection name: ' . $collectionName);
        }

        $collectionClass = $this->entityMap[$collectionName];
        if (!class_exists($collectionClass)) {
            throw new Exception('Not a valid class: ' . $collectionClass);
        }

        $entityName = str_replace('collection', '', $collectionName);
        $hydratedObjects = array_map(fn($row) => $this->Mapping($entityName, $row), $data);

        return new $collectionClass($hydratedObjects);
    }
}