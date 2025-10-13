<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Model\Status;
use API_ProfilingRepositories\StatusRepository;
use API_RelationRepositories\StatusRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\StatusRelations;
use TS_Exception\Classes\DomainException;

class StatusFactory extends CollectableFactory
{
    private StatusRelationRepository $statusRelationRepository;
    private StatusRelations $statusRelations;

    public function __construct(StatusRepository $repository, StatusRelationRepository $statusRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->statusRelationRepository = $statusRelationRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->id)->toArray();
            $relations = $this->statusRelationRepository->getBy([['StatusID', 'in', $ids]]);
        }
        $this->statusRelations = new StatusRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $statuses = [];
        if ($this->collection)
            $statuses = $this->collection->select(fn($n) => new Status($n, $this->statusRelations))->toArray();

        $this->collectable = new Statuses($statuses);
    }
}