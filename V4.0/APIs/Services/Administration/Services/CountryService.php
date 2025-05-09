<?php

namespace API_Administration_Service;

use API_Administration_Contract\ICountryService;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Factory\CountryFactory;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Country;
use Exception;

class CountryService implements ICountryService
{
    protected CountryFactory $countryFactory;
    protected CityFactory $cityFactory;

    public function __construct(CountryFactory $_countryFactory, CityFactory $_cityFactory)
    {
        $this->countryFactory = $_countryFactory;
        $this->cityFactory = $_cityFactory;
    }

    /**
     * @throws Exception
     */
    public function GetCountries(callable $predicate = null): Country|Countries|null
    {
        $this->countryFactory->Create();

        if (is_null($predicate))
            return $this->countryFactory->Collectable();

        $collection = $this->countryFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws Exception
     */
    public function GetCities(callable $predicate = null): City|Cities|null
    {
        $this->cityFactory->Create();

        if (is_null($predicate))
            return $this->cityFactory->Collectable();

        $collection = $this->cityFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}