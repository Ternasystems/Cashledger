<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingRepositories\GenderRepository;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class GenderFactory extends CollectableFactory
{
    protected GenderRelationRepository $relations;
    public function __construct(GenderRepository $repository, GenderRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
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
            $colArray[] = new Gender($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Genders($colArray);
    }

    public function Collectable(): ?Genders
    {
        return $this->collectable;
    }

    public function Repository(): GenderRepository
    {
        return $this->repository;
    }
}