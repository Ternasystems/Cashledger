<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Model\App;
use API_DTORepositories\AppRepository;
use API_RelationRepositories\AppRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class AppFactory extends CollectableFactory
{
    protected AppRelationRepository $relations;
    public function __construct(AppRepository $repository, AppRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
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
            $colArray[] = new App($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Apps($colArray);
    }

    public function Collectable(): ?Apps
    {
        return $this->collectable;
    }

    public function Repository(): AppRepository
    {
        return $this->repository;
    }
}