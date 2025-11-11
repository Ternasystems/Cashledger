<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ICityService;
use API_Administration_Contract\IContinentService;
use API_Administration_Contract\ICountryService;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CountryController extends BaseController
{
    protected IContinentService $continentService;
    protected ICountryService $countryService;
    protected ICityService $cityService;

    public function __construct(IContinentService $continentService, ICountryService $countryService, ICityService $cityService)
    {
        $this->continentService = $continentService;
        $this->countryService = $countryService;
        $this->cityService = $cityService;
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
     *
     * Supports suffixes like _like, _gt, _lt, _gte, _lte, _neq.
     *
     * @param array|null $filter e.g., ['ContinentId' => 'uuid-1', 'Name_like' => 'Test%']
     * @return array|null e.g., [['ContinentId', '=', 'uuid-1'], ['Name', 'LIKE', 'Test%']]
     */
    protected function parseFilter(?array $filter): ?array
    {
        if (is_null($filter)) {
            return null;
        }

        $whereClause = [];
        $operatorMap = ['_like' => 'LIKE', '_gt' => '>', '_gte' => '>=', '_lt' => '<', '_lte' => '<=', '_neq' => '!='];

        foreach ($filter as $key => $value) {
            $column = $key;
            $operator = '='; // Default operator

            foreach ($operatorMap as $suffix => $sqlOperator) {
                if (str_ends_with($key, $suffix)) {
                    $column = substr($key, 0, -strlen($suffix));
                    $operator = $sqlOperator;
                    break;
                }
            }

            $whereClause[] = [$column, $operator, $value];
        }
        return $whereClause;
    }

    /**
     * Gets a paginated list of continents, countries and cities.
     * Can be filtered, for example, by continent ID or Country ID.
     * E.g., /index.php?controller=Continent&action=index&page=1&pageSize=10
     * E.g., /index.php?controller=Country&action=index&filter[ContinentId]=1&page=1&pageSize=10
     * E.g., /index.php?controller=City&action=index&filter[CountryId]=1&page=1&pageSize=10
     */
    public function index(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Country');
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        // 1. Get the simple filter array from the URL
        $filterParams = $request->getQuery('filter'); // e.g., ['Name_like' => 'New%']

        // 2. Convert it into the format our services expect
        $filter = $this->parseFilter($filterParams); // e.g., [['Name', 'LIKE', 'New%']]

        $result = $controller == 'Continent' ? $this->continentService->getContinents($filter, $page, $pageSize, $reload) :
            ($controller == 'Country' ? $this->countryService->getCountries($filter, $page, $pageSize, $reload) : $this->cityService->getCities($filter, $page, $pageSize, $reload));
        return $this->json($result);
    }

    /**
     * Creates a new Continent, Country, or City.
     * Expects a POST request with a JSON body.
     * /index.php?controller=Country&action=store
     * /index.php?controller=Continent&action=store
     * /index.php?controller=City&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Country');
        $data = json_decode($request->content, true);

        if (!$data) {
            return $this->json(['error' => 'Invalid or empty JSON body.'], 400);
        }

        try {
            $newItem = match ($controller) {
                'Continent' => $this->continentService->setContinent($data),
                'Country' => $this->countryService->setCountry($data),
                'City' => $this->cityService->setCity($data),
                default => throw new Exception('Invalid controller context for store action.'),
            };

            return $this->json($newItem, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing Continent, Country, or City.
     * Expects a POST/PUT request with a JSON body and an ID in the query string.
     * /index.php?controller=Country&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Country');
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID parameter and JSON body are required.'], 400);
        }

        try {
            $updatedItem = match ($controller) {
                'Continent' => $this->continentService->putContinent($id, $data),
                'Country' => $this->countryService->putCountry($id, $data),
                'City' => $this->cityService->putCity($id, $data),
                default => throw new Exception('Invalid controller context for update action.'),
            };

            if (!$updatedItem) {
                return $this->json(['error' => 'Resource not found.'], 404);
            }
            return $this->json($updatedItem, 200); // 200 OK
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a Continent, Country, or City by its ID.
     * /index.php?controller=Country&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Country');
        $id = $request->getQuery('id');

        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $success = match ($controller) {
                'Continent' => $this->continentService->deleteContinent($id),
                'Country' => $this->countryService->deleteCountry($id),
                'City' => $this->cityService->deleteCity($id),
                default => throw new Exception('Invalid controller context for destroy action.'),
            };

            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete resource.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}