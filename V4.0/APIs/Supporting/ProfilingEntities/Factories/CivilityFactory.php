<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingRepositories\CivilityRepository;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class CivilityFactory extends CollectableFactory
{
    protected CivilityRelationRepository $relations;
    public function __construct(CivilityRepository $repository, CivilityRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
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
            $colArray[] = new Civility($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Civilities($colArray);
    }

    public function Collectable(): ?Civilities
    {
        return $this->collectable;
    }

    public function Repository(): CivilityRepository
    {
        return $this->repository;
    }
}