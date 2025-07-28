<?php

namespace API_ProfilingRepositories_Context;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Context\TContext;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Model\Token;
use Exception;
use TS_Database\Classes\DBConnection;
use TS_Database\Classes\DBContext;
use TS_Database\Classes\DBCredentials;
use TS_Exception\Classes\DBException;

/**
 * A specialized context for handling dynamically named role tables (e.g., cl_Role_Administrator).
 * It overrides the standard Select methods to build queries with dynamic table names.
 */
class TokenContext implements IContext
{
    // Note: We do NOT use the TContext trait here because we need to
    // implement custom SelectAll and SelectById logic.

    // This trait provides all the data access and mapping methods.
    use TContext;

    private DBContext $dbContext;
    protected array $entityMap = [];
    protected array $propertyMap = [];

    /**
     * @throws DBException
     */
    public function __construct(DBCredentials $credentials)
    {
        $pdo = DBConnection::create($credentials);
        $this->dbContext = new DBContext($pdo);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'token' => Token::class,
            'tokencollection' => Tokens::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'RoleID' => 'RoleId'
        ];
    }

    /**
     * Selects all records from a DYNAMICALLY specified table name.
     *
     * @param string $tableName The full name of the role table (e.g., 'cl_Role_Administrator').
     * @return array
     * @throws DBException
     */
    public function SelectAll(string $tableName): array
    {
        // The entityName parameter is treated as the full table name.
        $sql = sprintf('SELECT * FROM "%s" WHERE "IsActive" IS NULL', $tableName);
        return $this->dbContext->select($sql);
    }

    /**
     * Selects a single record by its ID from a DYNAMICALLY specified table name.
     *
     * @param string $Id The ID of the token record.
     * @param string $tableName The full name of the role table (e.g., 'cl_Role_Administrator').
     * @return array|null
     * @throws DBException
     */
    public function SelectById(string $Id, string $tableName): ?array
    {
        // The entityName parameter is treated as the full table name.
        $sql = sprintf('SELECT * FROM "%s" WHERE "ID" = ? AND "IsActive" IS NULL', $tableName);
        return $this->dbContext->selectOne($sql, [$Id]);
    }

    // --- CUD Operations are intentionally not supported at the generic context level ---

    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entityName = str_replace('cl_', '', $entityName);
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entityName = str_replace('cl_', '', $entityName);
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entityName = str_replace('cl_', '', $entityName);
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entityName = str_replace('cl_', '', $entityName);
        $sql = $this->SetStatement($entityName, $args);
        $this->dbContext->execute($sql, $args);
    }

    // --- Hydration methods are safe to include ---

    public function Mapping(string $entityName, array $data): ?DTOBase
    {
        if (!isset($this->entityMap[$entityName])) {
            throw new Exception('Not a valid entity name: ' . $entityName);
        }

        $entityClass = $this->entityMap[$entityName];
        $entity = new $entityClass();
        foreach ($data as $key => $value) {
            $propertyName = $this->propertyMap[$key] ?? $key;
            if (property_exists($entity, $propertyName)) {
                $entity->{$propertyName} = $value;
            }
        }
        return $entity;
    }

    public function MappingCollection(string $collectionName, array $data): ?Collectable
    {
        if (!isset($this->entityMap[$collectionName])) {
            throw new Exception('Not a valid collection name: ' . $collectionName);
        }

        $collectionClass = $this->entityMap[$collectionName];
        $entityName = str_replace('collection', '', $collectionName);
        $hydratedObjects = array_map(fn($row) => $this->Mapping($entityName, $row), $data);

        return new $collectionClass($hydratedObjects);
    }

}