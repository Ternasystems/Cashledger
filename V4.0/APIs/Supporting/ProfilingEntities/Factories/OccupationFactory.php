<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingRepositories\OccupationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\OccupationRelationRepository;
use Exception;

class OccupationFactory extends CollectableFactory
{
    protected OccupationRelationRepository $relations;
    public function __construct(OccupationRepository $repository, OccupationRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
    {
        parent::__construct($repository, $_relationRepository);
        $this->relations = $_relations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new Occupation($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Occupations($colArray);
    }

    public function Collectable(): ?Occupations
    {
        return $this->collectable;
    }

    public function Repository(): OccupationRepository
    {
        return $this->repository;
    }
}