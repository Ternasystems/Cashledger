<?php

namespace API_DTORepositories;

use API_Assets\Classes\DTOException;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Contract\IRepository;
use API_DTORepositories_Model\DTOBase;
use Exception;
use ReflectionClass;
use ReflectionException;
use TS_Database\Enums\OrderByDirection;
use TS_Domain\Interfaces\ISpecification;

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
     * The constructor now accepts the specific model and collection classes,
     * eliminating the need for reflection and overrides.
     *
     * @param IContext $context The data context.
     * @param class-string<T> $modelClass The DTO model class.
     * @param class-string<TCollection> $collectionClass The DTO collection class.
     * @throws ReflectionException
     */
    public function __construct(IContext $context, string $modelClass, string $collectionClass)
    {
        $this->context = $context;
        $this->modelClass = $modelClass;

        // Derive the short names from the full class names.
        $this->entityName = strtolower(new ReflectionClass($modelClass)->getShortName());
        $this->collectionName = strtolower(new ReflectionClass($collectionClass)->getShortName());
    }

    /**
     * @param array|null $whereClause
     * @return DTOBase|null
     */
    public function first(?array $whereClause = null): ?DTOBase
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
     * @return DTOBase|null
     */
    public function getById(string $id): ?DTOBase
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
     * Finds entities that satisfy a given specification by fetching all
     * records and filtering them in memory.
     *
     * @param ISpecification $specification The specification to apply.
     * @return Collectable|null A collection of matching entities.
     */
    public function find(ISpecification $specification): ?Collectable
    {
        $entities = $this->getAll();

        return $entities?->where(
            fn(DTOBase $entity) => $specification->isSatisfiedBy($entity)
        );

    }

    /**
     * @param array|null $whereClause
     * @return DTOBase|null
     */
    public function last(?array $whereClause = null): ?DTOBase
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