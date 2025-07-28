<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IContext;
use API_DTORepositories_Contract\IRepository;
use API_DTORepositories_Model\DTOBase;
use Closure;
use Exception;
use ReflectionClass;

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
     * The fully qualified class name of the DTO model (e.g., "API_DTORepositories_Model\App").
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
            throw new Exception('Repository getById method must have a specific return type hint.');
        }

        $this->modelClass = $returnType->getName();

        // Derive the short names from the full class name.
        $classNameParts = explode('\\', $this->modelClass);
        $this->entityName = strtolower(end($classNameParts));
        $this->collectionName = $this->entityName . 'collection';
    }

    /**
     * {@inheritDoc}
     * @return T|null
     */
    public function first(?Closure $predicate = null): ?object
    {
        $collection = $this->getAll();
        return $collection?->first($predicate);
    }

    /**
     * {@inheritDoc}
     * @return TCollection|null
     */
    public function getAll(): ?Collectable
    {
        $data = $this->context->SelectAll($this->entityName);
        if (empty($data)) {
            return null;
        }
        return $this->context->MappingCollection($this->collectionName, $data);
    }

    /**
     * {@inheritDoc}
     * @return T|null
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
     * {@inheritDoc}
     * @return TCollection|null
     */
    public function getBy(Closure $predicate): ?Collectable
    {
        $collection = $this->getAll();
        return $collection?->where($predicate);
    }

    /**
     * {@inheritDoc}
     * @return T|null
     */
    public function last(?Closure $predicate = null): ?object
    {
        $collection = $this->getAll();
        return $collection?->last($predicate);
    }

    public function add(array $args): void
    {
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

    public function update(array $args): void
    {
        $this->context->Update($this->modelClass, $args);
    }
}