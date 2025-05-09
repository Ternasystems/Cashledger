<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ICountryService;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Country;
use TS_Controller\Classes\BaseController;

class CountryController extends BaseController
{
    private ICountryService $service;

    public function __construct(ICountryService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Countries
    {
        return $this->service->GetCountries();
    }

    public function GetById(string $Id): ?Country
    {
        return $this->service->GetCountries(fn($n) => $n->It()->Id == $Id);
    }

    public function GetByISO(int $isoType, string $iso): ?Country
    {
        return $this->service->GetCountries(fn($n) => $n->It()->{$isoType == 2 ? 'Iso2' : 'Iso3'} == $iso);
    }

    public function GetCities(): ?Cities
    {
        return $this->service->GetCities();
    }

    public function GetCityById(string $Id): ?City
    {
        return $this->service->GetCities(fn($n) => $n->It()->Id == $Id);
    }
}