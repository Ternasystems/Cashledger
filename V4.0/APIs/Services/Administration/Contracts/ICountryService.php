<?php

namespace API_Administration_Contract;

use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Country;

interface ICountryService
{
    public function GetCountries(callable $predicate = null): Country|Countries|null;
    public function GetCities(callable $predicate = null): City|Cities|null;
}