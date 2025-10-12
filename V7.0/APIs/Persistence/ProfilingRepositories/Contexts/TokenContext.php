<?php

namespace API_ProfilingRepositories_Context;

use API_Assets\Classes\DTOException;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Model\Token;
use TS_Database\Classes\DBContext;
use TS_Database\Classes\DBCredentials;
use TS_Database\Enums\OrderByDirection;
use TS_Database\Enums\WhereType;
use TS_Exception\Classes\DBException;
use PDOStatement;

class TokenContext implements IContext
{
    protected DBContext $dbContext;
    protected array $entityMap = [];
    protected array $propertyMap = [];
    protected string $prefix = 'cl_Role_';

    /**
     * Initializes the context, creates the database connection, and calls the
     * abstract methods to configure the data maps.
     *
     * @param DBCredentials $credentials
     * @throws DBException
     */
    public function __construct(DBCredentials $credentials)
    {
        $this->dbContext = new DBContext($credentials);
        $this->setEntityMap();
        $this->setPropertyMap();
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
     * @inheritDoc
     * @throws DBException
     */
    public function SelectAll(string $entityName, ?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): array
    {
        if (!Token::hasTableName())
            Token::setTableName($this->prefix . $entityName);

        $builder = Token::query();

        if (!is_null($whereClause)) {
            foreach ($whereClause as $value)
                $builder->where($value['column'], $value['operator'], $value['value'], $value['type'] ?? WhereType::AND);
        }
        $builder->where('IsActive', '=', null);

        if (!is_null($orderBy)){
            foreach ($orderBy as $value)
                $builder->orderBy($value['column'], $value['direction'] ?? OrderByDirection::ASC);
        }
        $builder->limit($limit, $offset);

        return $builder->get();
    }

    /**
     * @inheritDoc
     * @throws DBException
     */
    public function SelectById(string $Id, string $entityName): ?array
    {
        if (!Token::hasTableName())
            Token::setTableName($this->prefix . $entityName);

        // Use the model's query builder to find a specific active record by its ID.
        $result = Token::query()
            ->where('ID', '=', $Id)
            ->where('IsActive', '=', null)
            ->get();

        return $result[0] ?? null;
    }

    protected function SetStatement(string $entityName): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerFunction = $backtrace[1]['function']; // e.g., "Insert", "Update"

        return 'p_' . $callerFunction . 'Role_' . $entityName;
    }

    /**
     * @inheritDoc
     * @throws DBException
     */
    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /**
     * @inheritDoc
     * @throws DBException
     */
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /**
     * @inheritDoc
     * @throws DBException
     */
    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /**
     * @inheritDoc
     * @throws DBException
     */
    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $procName = $this->SetStatement($entityName);
        /** @var DTOBase $entityName */
        $entityName::query()->call($procName, $args);
    }

    /**
     * @throws DBException
     */
    public function Prepare(string $sql): PDOStatement
    {
        return $this->dbContext->prepare($sql);
    }

    /**
     * @inheritDoc
     * @throws DTOException
     */
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

    /**
     * @inheritDoc
     * @throws DTOException
     */
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

    public function beginTransaction(): bool
    {
        return $this->dbContext->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->dbContext->commit();
    }

    public function rollBack(): bool
    {
        return $this->dbContext->rollBack();
    }
}