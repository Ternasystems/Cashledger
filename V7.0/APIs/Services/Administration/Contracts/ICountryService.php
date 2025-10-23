<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Continent;
use API_DTOEntities_Model\Country;

interface ICountryService
{
    public function GetContinents(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Continent|Continents|null;
    public function GetCountries(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Country|Countries|null;
    public function GetCities(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): City|Cities|null;
}