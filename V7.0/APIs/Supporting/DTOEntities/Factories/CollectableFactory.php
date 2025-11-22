<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\EntityCollectable;
use API_DTOEntities_Contract\ICollectableFactory;
use API_DTOEntities_Model\Entity;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\LanguageRelations;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use TS_Domain\Classes\AbstractCollectable;
use TS_Exception\Classes\DomainException;

class CollectableFactory implements ICollectableFactory
{
    protected IRepository $repository;
    protected LanguageRelationRepository $languageRelationRepository;
    protected EntityCollectable|AbstractCollectable|null $collectable;

    /** @var class-string<Entity> The fully qualified class name of the entity to create. */
    protected string $entityClass;

    /** @var class-string<EntityCollectable> The fully qualified class name of the collection to create. */
    protected string $collectionClass;

    protected Collectable $collection;
    protected ?LanguageRelations $languageRelations;

    protected ?array $whereClause = null;
    protected ?int $limit = null;
    protected ?int $offset = null;

    /**
     * Initializes a new instance of the generic CollectableFactory.
     *
     * @param IRepository $repository The repository for the entity (e.g., ContinentRepository).
     * @param LanguageRelationRepository $languageRelationRepository The common translations' repository.
     * @throws ReflectionException
     */
    public function __construct(IRepository $repository, LanguageRelationRepository $languageRelationRepository)
    {
        $this->repository = $repository;
        $this->languageRelationRepository = $languageRelationRepository;

        $class = new ReflectionClass($this->repository);
        $namespace = str_replace('Repositories', 'Entities', $class->getNamespaceName()).'_Model';
        $this->entityClass = $namespace.'\\'.str_replace('Repository', '', $class->getShortName());

        $name = strtolower(str_replace('Repository', 'collection', $class->getShortName()));
        $this->collectionClass = str_replace('Repositories', 'Entities', $this->repository->context->GetCollectionClassName($name));

        $this->collectable = null;
    }

    public function filter(?array $_whereClause = null, ?int $_limit = null, ?int $_offset = null): void
    {
        $this->whereClause = $_whereClause;
        $this->limit = $_limit;
        $this->offset = $_offset;
    }

    public function getFilter(): ?array
    {
        return (is_null($this->whereClause) && is_null($this->limit) && is_null($this->offset)) ? null : array($this->whereClause, $this->limit, $this->offset);
    }

    /**
     * @throws DomainException
     */
    public function Create(): void
    {
        // Fetch all necessary data first.
        $this->fetchDependencies();
        $this->fetchLanguageRelations();

        if (!is_null($this->languageRelations))
            $this->entityClass::LanguageRelationProvider($this->languageRelations);

        $this->build();

        // Clean up the static provider to prevent memory leaks across different factory uses.
        $this->entityClass::LanguageRelationProvider(null);
    }

    /**
     * @throws DomainException
     */
    protected function fetchLanguageRelations(): void
    {
        $languages = [];

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $languages = $this->languageRelationRepository->getBy([['ReferenceID', 'in', $ids]])?->toArray();
        }
        $this->languageRelations = is_null($languages) ? null : new LanguageRelations($languages);
    }

    /**
     * For this generic factory, we assume no additional dependencies are needed.
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);
    }

    /**
     * Builds the collection using the class names provided in the constructor.
     */
    protected function build(): void
    {
        $entities = [];
        if ($this->collection)
            $entities = $this->collection->select(fn($n) => new $this->entityClass($n))->toArray();

        // Dynamically instantiate the collection class.
        // e.g., new Continents($entities)
        $this->collectable = new $this->collectionClass($entities);
    }

    public function collectable(): EntityCollectable|AbstractCollectable|null
    {
        return $this->collectable;
    }

    public function repository(): IRepository
    {
        return $this->repository;
    }

    public function reset(): void
    {
        $this->collectable = null;
    }

    public function toArray(): ?array
    {
        return $this->collectable?->toArray();
    }
}