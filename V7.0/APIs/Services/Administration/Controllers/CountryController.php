<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ICityService;
use API_Administration_Contract\IContinentService;
use API_Administration_Contract\ICountryService;
use API_Administration_Facade\CountryFacade;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CountryController extends BaseController
{
    /**
     * Inject the concrete facade for type safety, as we discussed.
     */
    public function __construct(protected CountryFacade $facade){}

    /**
     * Gets a paginated list of continents, countries and cities.
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Country');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            // Call the facade
            $result = $this->facade->get($controller, $filter, $page, $pageSize, $reload);

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new Continent, Country, or City.
     */
    public function store(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Country');
            $data = json_decode($request->content, true);

            if (!$data) {
                return $this->json(['error' => 'Invalid or empty JSON body.'], 400);
            }

            // Call the facade
            $newItem = $this->facade->set($controller, $data);

            return $this->json($newItem, 201); // 201 Created

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing Continent, Country, or City.
     */
    public function update(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Country');
            $id = $request->getQuery('id');
            $data = json_decode($request->content, true);

            if (!$id || !$data) {
                return $this->json(['error' => 'ID parameter and JSON body are required.'], 400);
            }

            // Call the facade
            $updatedItem = $this->facade->put($controller, $id, $data);

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
     */
    public function destroy(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Country');
            $id = $request->getQuery('id');

            if (!$id) {
                return $this->json(['error' => 'ID parameter is required.'], 400);
            }

            // Call the facade
            $success = $this->facade->delete($controller, $id);

            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete resource.'], 500);

        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}