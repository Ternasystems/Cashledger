<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Collection\EntityCollectable;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Model\Teller;
use API_TellerRepositories\TellerRepository;
use TS_Exception\Classes\DomainException;

class TellerFactory extends CollectableFactory
{
    private CollectableFactory $factory;
    private EntityCollectable $profiles;

    public function __construct(TellerRepository $repository, CollectableFactory $factory, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $factory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->ProfileId)->toArray();
            $this->factory->filter([['ProfileID', 'in', $ids]]);
        }

        $this->factory->Create();
        $this->profiles = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $tellers = [];
        if ($this->collection)
            $tellers = $this->collection->select(fn($n) => new Teller($n, $this->profiles[$n->ProfileId]))->toArray();

        $this->collectable = new Tellers($tellers);
    }
}