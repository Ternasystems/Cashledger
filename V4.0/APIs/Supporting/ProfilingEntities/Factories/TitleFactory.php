<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Title;
use API_ProfilingRepositories\TitleRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\TitleRelationRepository;
use Exception;

class TitleFactory extends CollectableFactory
{
    protected TitleRelationRepository $relations;
    public function __construct(TitleRepository $repository, TitleRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
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
            $colArray[] = new Title($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new Titles($colArray);
    }

    public function Collectable(): ?Titles
    {
        return $this->collectable;
    }

    public function Repository(): TitleRepository
    {
        return $this->repository;
    }
}