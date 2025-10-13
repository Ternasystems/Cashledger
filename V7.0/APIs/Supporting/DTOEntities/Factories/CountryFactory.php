<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Model\Country;
use API_DTORepositories\ContinentRepository;
use API_DTORepositories\CountryRepository;
use API_DTORepositories_Collection\Countries;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class CountryFactory extends CollectableFactory
{
    private ContinentRepository $continentRepository;
    private CollectableFactory $factory;
    private Continents $continents;

    public function __construct(CountryRepository $repository, ContinentRepository $continentRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->continentRepository = $continentRepository;

        $this->factory = new CollectableFactory($this->continentRepository, $languageRelationRepository);

    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $this->factory->Create();
        $this->continents = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $countries = [];
        if ($this->collection)
            $countries = $this->collection->select(fn($n) => new Country($n, $this->continents[$n->ContinentId]))->toArray();

        $this->collectable = new Countries($countries);
    }
}