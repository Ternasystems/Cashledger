<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Contract\IRepository;
use API_DTORepositories_Model\DTOBase;
use Exception;
use ReflectionClass;
use TS_Database\Enums\OrderByDirection;

/**
 * An abstract base class for repositories that implements the IRepository interface.
 * It uses reflection to automatically determine the entity it manages, reducing
 * boilerplate in concrete repository classes.
 *
 * @template T of DTOBase
 * @template TCollection of Collectable
 * @implements IRepository<T, TCollection>
 */
abstract class Repository implements IRepository
{
    protected IContext $context;

    /**
     * The fully qualified class name of the DTO model (e.g., "API\Supporting\DTOEntities\Models\App").
     * @var class-string<T>
     */
    protected string $modelClass;

    /**
     * The short name of the entity used for context mapping (e.g., "app").
     * @var string
     */
    protected string $entityName;

    /**
     * The short name of the collection used for context mapping (e.g., "appcollection").
     * @var string
     */
    protected string $collectionName;

    /**
     * The constructor uses reflection on the concrete class's `getById` method
     * to determine the entity, model, and collection names one time.
     *
     * @param IContext $context The data context.
     * @throws Exception if the return type of getById is not a valid class.
     */
    public function __construct(IContext $context)
    {
        $this->context = $context;

        // Use reflection to get the return type of the concrete getById method.
        $reflection = new ReflectionClass(static::class);
        $method = $reflection->getMethod('getById');
        $returnType = $method->getReturnType();

        if (!$returnType || !method_exists($returnType, 'getName')) {
            throw new DTOException('invalid_argument', [':reason' => 'Repository getById method must have a specific return type hint.']);
        }

        /** @var class-string<T> $modelClass */
        $modelClass = $returnType->getName();
        $this->modelClass = $modelClass;

        // Derive the short names from the full class name.
        $classNameParts = explode('\\', $this->modelClass);
        $this->entityName = strtolower(end($classNameParts));
        $this->collectionName = $this->entityName . 'collection';
    }

    /**
     * @param array|null $whereClause
     * @return object|null
     */
    public function first(?array $whereClause = null): ?object
    {
        return $this->getBy($whereClause, 1, null, [['ID', OrderByDirection::ASC]])?->first();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $orderBy
     * @return Collectable|null
     */
    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Collectable
    {
        return $this->getBy(null, $limit, $offset, $orderBy);
    }

    /**
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object
    {
        $data = $this->context->SelectById($id, $this->entityName);
        if (empty($data)) {
            return null;
        }
        return $this->context->Mapping($this->entityName, $data);
    }

    /**
     * @param array|null $whereClause
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $orderBy
     * @return Collectable|null
     */
    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Collectable
    {
        $data = $this->context->SelectAll($this->entityName, $whereClause, $limit, $offset, $orderBy);
        if (empty($data)) {
            return null;
        }
        return $this->context->MappingCollection($this->collectionName, $data);
    }

    /**
     * @param array|null $whereClause
     * @return object|null
     */
    public function last(?array $whereClause = null): ?object
    {
        return $this->getBy($whereClause, 1, null, [['ID', OrderByDirection::DESC]])?->last();
    }

    public function add(DTOBase $entity): void
    {
        $args = get_object_vars($entity);
        $this->context->Insert($this->modelClass, $args);
    }

    public function remove(string $id): void
    {
        $this->context->Delete($this->modelClass, [$id]);
    }

    public function deactivate(string $id): void
    {
        $this->context->Disable($this->modelClass, [$id]);
    }

    public function update(DTOBase $entity): void
    {
        $args = get_object_vars($entity);
        $this->context->Update($this->modelClass, $args);
    }
}