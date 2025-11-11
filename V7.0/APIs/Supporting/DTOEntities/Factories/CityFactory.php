<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Model\City;
use API_DTORepositories\CityRepository;
use API_DTORepositories_Collection\Countries;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class CityFactory extends CollectableFactory
{
    private CountryFactory $factory;
    private Countries $countries;

    public function __construct(CityRepository $repository, CountryFactory $countries, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $countries;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->CountryId)->toArray();
            $this->factory->filter([['CountryID', 'in', $ids]]);
        }

        $this->factory->Create();
        $this->countries = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $cities = [];
        if ($this->collection)
            $cities = $this->collection->select(fn($n) => new City($n, $this->countries[$n->CountryId]))->toArray();

        $this->collectable = new Cities($cities);
    }
}