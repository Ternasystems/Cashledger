<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Contract\IRepository;
use API_DTORepositories_Model\DTOBase;
use Closure;
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

    public function first(?Closure $predicate = null): ?object
    {
        if ($predicate) {
            return $this->getAll()?->first($predicate);
        }

        $data = $this->modelClass::query()
            ->orderBy('ID', OrderByDirection::ASC)
            ->limit(1)
            ->get();

        if (empty($data[0])) {
            return null;
        }
        return $this->context->Mapping($this->entityName, $data[0]);
    }

    public function getAll(): ?Collectable
    {
        $data = $this->context->SelectAll($this->entityName);
        if (empty($data)) {
            return null;
        }
        return $this->context->MappingCollection($this->collectionName, $data);
    }

    public function getById(string $id): ?object
    {
        $data = $this->context->SelectById($id, $this->entityName);
        if (empty($data)) {
            return null;
        }
        return $this->context->Mapping($this->entityName, $data);
    }

    public function getBy(Closure $predicate): ?Collectable
    {
        $collection = $this->getAll();
        return $collection?->where($predicate);
    }

    public function last(?Closure $predicate = null): ?object
    {
        if ($predicate) {
            return $this->getAll()?->last($predicate);
        }

        $data = $this->modelClass::query()
            ->orderBy('ID', OrderByDirection::DESC)
            ->limit(1)
            ->get();

        if (empty($data[0])) {
            return null;
        }
        return $this->context->Mapping($this->entityName, $data[0]);
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