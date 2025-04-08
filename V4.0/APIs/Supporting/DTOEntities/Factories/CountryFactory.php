<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\Country;
use API_DTORepositories\ContinentRepository;
use API_DTORepositories\CountryRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;
use ReflectionException;

class CountryFactory extends CollectableFactory
{
    protected Continents $continents;

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function __construct(CountryRepository $repository, ContinentRepository $_continents, ?LanguageRelationRepository $_relationRepository)
    {
        parent::__construct($repository, $_relationRepository);
        $factory = new CollectableFactory($_continents, $_relationRepository);
        $factory->Create();
        $this->continents = $factory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $continent = $this->continents->FirstOrDefault(fn($n) => $n->It()->Id == $item->ContinentId);
            $colArray[] = new Country($item, $continent, $this->relationRepository->GetAll());
        }

        $this->collectable = new Countries($colArray);
    }

    public function Collectable(): ?Countries
    {
        return $this->collectable;
    }

    public function Repository(): CountryRepository
    {
        return $this->repository;
    }
}