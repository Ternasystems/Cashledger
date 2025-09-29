<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingRepositories\CivilityRepository;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\CivilityRelations;
use TS_Exception\Classes\DomainException;

class CivilityFactory extends CollectableFactory
{
    private CivilityRelationRepository $civilityRelationRepository;
    private CivilityRelations $civilityRelations;

    public function __construct(CivilityRepository $repository, CivilityRelationRepository $civilityRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->civilityRelationRepository = $civilityRelationRepository;
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
            $relations = $this->civilityRelationRepository->getBy([['CivilityID', 'in', $ids]]);
        }
        $this->civilityRelations = new CivilityRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $civilities = [];
        if ($this->collection)
            $civilities = $this->collection->select(fn($n) => new Civility($n, $this->civilityRelations))->toArray();

        $this->collectable = new Civilities($civilities);
    }
}