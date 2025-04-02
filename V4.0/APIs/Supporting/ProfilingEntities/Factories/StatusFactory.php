<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Model\Status;
use API_ProfilingRepositories\StatusRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\StatusRelationRepository;
use Exception;

class StatusFactory extends CollectableFactory
{
    protected StatusRelationRepository $relations;
    public function __construct(StatusRepository $repository, StatusRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
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
            $colArray[] = new Status($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Statuses($colArray);
    }

    public function Collectable(): ?Statuses
    {
        return $this->collectable;
    }

    public function Repository(): StatusRepository
    {
        return $this->repository;
    }
}