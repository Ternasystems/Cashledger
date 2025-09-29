<?php

namespace API_Administration_Service;

use API_Administration_Contract\ICountryService;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Factory\CountryFactory;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Continent;
use API_DTOEntities_Model\Country;
use API_DTORepositories\ContinentRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use TS_Exception\Classes\DomainException;

class CountryService implements ICountryService
{
    protected ContinentRepository $continentRepository;
    protected Continents $continents;
    protected CollectableFactory $factory;
    protected CountryFactory $countryFactory;
    protected Countries $countries;
    protected CityFactory $cityFactory;
    protected Cities $cities;
    protected LanguageRelationRepository $relationRepository;

    public function __construct(CountryFactory $_countryFactory, CityFactory $_cityFactory, ContinentRepository $_continentRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->continentRepository = $_continentRepository;
        $this->countryFactory = $_countryFactory;
        $this->cityFactory = $_cityFactory;
        $this->relationRepository = $_relationRepository;
    }

    /**
     * @throws ReflectionException
     * @throws DomainException
     */
    public function GetContinents(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Continent|Continents|null
    {
        if (!isset($this->continents) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory = new CollectableFactory($this->continentRepository, $this->relationRepository);
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->continents = $this->factory->collectable();
        }

        if (count($this->continents) === 0)
            return null;

        return $this->continents->count() > 1 ? $this->continents : $this->continents->first();
    }

    /**
     * @throws DomainException
     */
    public function GetCountries(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Country|Countries|null
    {
        if (!isset($this->countries) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->countryFactory->filter($filter, $pageSize, $offset);
            $this->countryFactory->Create();
            $this->countries = $this->countryFactory->collectable();
        }

        if (count($this->countries) === 0)
            return null;

        return $this->countries->count() > 1 ? $this->countries : $this->countries->first();
    }

    /**
     * @throws DomainException
     */
    public function GetCities(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): City|Cities|null
    {
        if (!isset($this->cities) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->cityFactory->filter($filter, $pageSize, $offset);
            $this->cityFactory->Create();
            $this->cities = $this->countryFactory->collectable();
        }

        if (count($this->cities) === 0)
            return null;

        return $this->cities->count() > 1 ? $this->cities : $this->cities->first();
    }
}