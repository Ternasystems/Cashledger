<?php

namespace API_Administration_Facade;

use API_Administration_Contract\ICityService;
use API_Administration_Contract\IContinentService;
use API_Administration_Contract\ICountryService;
use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Continent;
use API_DTOEntities_Model\Country;
use Exception;

/**
 * This is the Facade class for Continent, Country, and City management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class CountryFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected IContinentService $continentService, protected ICountryService $countryService, protected ICityService $cityService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Continents|Continent|Countries|Country|Cities|City
    {
        return match ($resourceType) {
            'Continent' => $this->continentService->getContinents($filter, $page, $pageSize, $reloadMode),
            'Country' => $this->countryService->getCountries($filter, $page, $pageSize, $reloadMode),
            'City' => $this->cityService->getCities($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for CountryFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): Country|Continent|City
    {
        return match ($resourceType) {
            'Continent' => $this->continentService->setContinent($data),
            'Country' => $this->countryService->setCountry($data),
            'City' => $this->cityService->setCity($data),
            default => throw new Exception("Invalid resource type for CountryFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): Country|Continent|City|null
    {
        return match ($resourceType) {
            'Continent' => $this->continentService->putContinent($id, $data),
            'Country' => $this->countryService->putCountry($id, $data),
            'City' => $this->cityService->putCity($id, $data),
            default => throw new Exception("Invalid resource type for CountryFacade 'put': $resourceType"),
        };
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return match ($resourceType) {
            'Continent' => $this->continentService->deleteContinent($id),
            'Country' => $this->countryService->deleteCountry($id),
            'City' => $this->cityService->deleteCity($id),
            default => throw new Exception("Invalid resource type for CountryFacade 'delete': $resourceType"),
        };
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for CountryFacade 'disable': $resourceType");
    }
}