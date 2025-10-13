<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingRepositories\OccupationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\OccupationRelationRepository;
use API_RelationRepositories_Collection\OccupationRelations;
use TS_Exception\Classes\DomainException;

class OccupationFactory extends CollectableFactory
{
    private OccupationRelationRepository $occupationRelationRepository;
    private OccupationRelations $occupationRelations;

    public function __construct(OccupationRepository $repository, OccupationRelationRepository $occupationRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->occupationRelationRepository = $occupationRelationRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->occupationRelationRepository->getBy([['OccupationID', 'in', $ids]]);
        }
        $this->occupationRelations = new OccupationRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $occupations = [];
        if ($this->collection)
            $occupations = $this->collection->select(fn($n) => new Occupation($n, $this->occupationRelations))->toArray();

        $this->collectable = new Occupations($occupations);
    }
}