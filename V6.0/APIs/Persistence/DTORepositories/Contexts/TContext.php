<?php

namespace API_DTORepositories_Context;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use Exception;
use PDOStatement;
use ReflectionClass;
use ReflectionException;
use TS_Exception\Classes\DBException;

/**
 * A reusable trait that provides the core data mapping and database interaction logic.
 */
trait TContext
{
    /**
     * @throws DBException
     * @throws Exception
     */
    public function SelectAll(string $entityName): array
    {
        if (!isset($this->entityMap[$entityName])) {
            throw new DTOException('invalid_entity_name', [':name' => $entityName]);
        }

        /** @var DTOBase $modelClass */
        $modelClass = $this->entityMap[$entityName];

        // Use the model's own query builder to fetch all active records.
        // We assume the query builder correctly handles a `null` value as an `IS NULL` check.
        return $modelClass::query()->where('IsActive', '=', null)->get();
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function SelectById(string $Id, string $entityName): ?array
    {
        if (!isset($this->entityMap[$entityName])) {
            throw new DTOException('invalid_entity_name', [':name' => $entityName]);
        }

        /** @var DTOBase $modelClass */
        $modelClass = $this->entityMap[$entityName];

        // Use the model's query builder to find a specific active record by its ID.
        $result = $modelClass::query()
            ->where('ID', '=', $Id)
            ->where('IsActive', '=', null)
            ->get();

        return $result[0] ?? null;
    }

    /**
     * @throws ReflectionException
     */
    protected function SetStatement(string $entityName = DTOBase::class): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerFunction = $backtrace[1]['function']; // e.g., "Insert", "Update"

        $className = new ReflectionClass($entityName)->getShortName();
        return 'p_' . $callerFunction . $className;
    }

    /** @throws DBException|ReflectionException */
    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /** @throws DBException|ReflectionException */
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /** @throws DBException|ReflectionException */
    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /** @throws DBException|ReflectionException */
    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /** @throws DBException */
    public function ExecuteSelectAll(string $sql, array $params = []): array
    {
        return $this->dbContext->select($sql, $params);
    }

    /** @throws DBException */
    public function ExecuteSelectOne(string $sql, array $params = []): ?array
    {
        return $this->dbContext->selectOne($sql, $params);
    }

    /** @throws DBException */
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

    /** @throws Exception */
    public function Mapping(string $entityName, array $data): ?DTOBase
    {
        if (!isset($this->entityMap[$entityName])) {
            throw new DTOException('invalid_entity_name', [':name' => $entityName]);
        }

        $entityClass = $this->entityMap[$entityName];
        $entity = new $entityClass();

        foreach ($data as $key => $value) {
            $propertyName = $this->propertyMap[$key] ?? $key;
            $entity->{$propertyName} = $value; // Uses AbstractModel's __set
        }
        return $entity;
    }

    /** @throws Exception */
    public function MappingCollection(string $collectionName, array $data): ?Collectable
    {
        if (!isset($this->entityMap[$collectionName])) {
            throw new DTOException('invalid_collection_name', [':name' => $collectionName]);
        }

        $collectionClass = $this->entityMap[$collectionName];
        $entityName = str_replace('collection', '', $collectionName);
        $hydratedObjects = array_map(fn($row) => $this->Mapping($entityName, $row), $data);

        return new $collectionClass($hydratedObjects);
    }
}