<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ICountryService;
use API_Administration_Service\ReloadMode;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CountryController extends BaseController
{
    protected ICountryService $service;

    public function __construct(ICountryService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of continents.
     * Responds to URLs like: /index.php?controller=Country&action=continents
     */
    public function continents(Request $request): Response
    {
        $result = $this->service->GetContinents(null, null, null);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of countries.
     * Can be filtered, for example, by continent ID.
     * E.g., /index.php?controller=Country&action=index&filter[ContinentId]=1
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        // A simple filter implementation could read from GET parameters.
        $filter = $request->getQuery('filter'); // e.g., ['ContinentId' => 'continent-uuid-1']

        $result = $this->service->GetCountries($filter, $page, $pageSize, $reload);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of cities.
     * Can be filtered, for example, by country ID.
     */
    public function cities(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        $filter = $request->getQuery('filter');

        $result = $this->service->GetCities($filter, $page, $pageSize, $reload);
        return $this->json($result);
    }
}