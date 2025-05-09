<?php

namespace API_DTORepositories_Context;

use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Collection\Audits;
use API_DTORepositories_Collection\Cities;
use API_DTORepositories_Collection\Continents;
use API_DTORepositories_Collection\Countries;
use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Model\App;
use API_DTORepositories_Model\AppCategory;
use API_DTORepositories_Model\Audit;
use API_DTORepositories_Model\City;
use API_DTORepositories_Model\Continent;
use API_DTORepositories_Model\Country;
use API_DTORepositories_Model\Language;
use PDO;
use TS_Database\Classes\DBContext;

class DTOContext extends DBContext
{
    protected PDO $pdo;
    private string $app = 'cl_Apps';
    private string $appcategory = 'cl_AppCategories';
    private string $audit = 'cl_Audits';
    private string $city = 'cl_Cities';
    private string $continent = 'cl_Continents';
    private string $country = 'cl_Countries';
    private string $language = 'cl_Languages';

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    use TContext;

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'app' => App::class,
            'appcategory' => AppCategory::class,
            'audit' => Audit::class,
            'city' => City::class,
            'continent' => Continent::class,
            'country' => Country::class,
            'language' => Language::class,
            'appcollection' => Apps::class,
            'appcategorycollection' => AppCategories::class,
            'auditcollection' => Audits::class,
            'citycollection' => Cities::class,
            'continentcollection' => Continents::class,
            'countrycollection' => Countries::class,
            'languagecollection' => Languages::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'ContinentID' => 'ContinentId',
            'CountryID' => 'CountryId',
            'ISO2' => 'Iso2',
            'ISO3' => 'Iso3'
        ];
    }
}