<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\City;
use API_DTORepositories\CityRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class CityFactory extends CollectableFactory
{
    protected Countries $countries;

    /**
     * @throws Exception
     */
    public function __construct(CityRepository $repository, CountryFactory $_countryFactory, ?LanguageRelationRepository $_relationRepository)
    {
        parent::__construct($repository, $_relationRepository);
        $_countryFactory->Create();
        $this->countries = $_countryFactory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $country = $this->countries->FirstOrDefault(fn($n) => $n->It()->Id == $item->CountryId);
            $colArray[] = new City($item, $country, $this->relationRepository->GetAll());
        }

        $this->collectable = new Cities($colArray);
    }

    public function Collectable(): ?Cities
    {
        return $this->collectable;
    }

    public function Repository(): CityRepository
    {
        return $this->repository;
    }
}