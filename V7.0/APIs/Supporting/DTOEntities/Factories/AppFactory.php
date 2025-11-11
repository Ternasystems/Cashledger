<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Model\App;
use API_DTORepositories\AppRepository;
use API_DTORepositories_Collection\Apps;
use API_RelationRepositories\ApprelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\AppRelations;
use TS_Exception\Classes\DomainException;

class AppFactory extends CollectableFactory
{
    private AppRelationRepository $appRelationRepository;
    private AppRelations $appRelations;

    public function __construct(AppRepository $repository, AppRelationRepository $appRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->appRelationRepository = $appRelationRepository;
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
            $relations = $this->appRelationRepository->getBy([['AppID', 'in', $ids]]);
        }
        $this->appRelations = new AppRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $apps = [];
        if ($this->collection)
            $apps = $this->collection->select(fn($n) => new App($n, $this->appRelations->where(fn($t) => $t->AppId == $n->Id)))->toArray();

        $this->collectable = new Apps($apps);
    }
}