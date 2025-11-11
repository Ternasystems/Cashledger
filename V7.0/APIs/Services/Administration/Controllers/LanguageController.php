<?php

namespace API_Administration_Controller;

use API_Administration_Contract\ILanguageService;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class LanguageController extends BaseController
{
    protected ILanguageService $service;

    public function __construct(ILanguageService $service)
    {
        $this->service = $service;
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
     *
     * @param array|null $filter e.g., ['Name_like' => 'Eng%']
     * @return array|null e.g., [['Name', 'LIKE', 'Eng%']]
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
     * Gets a paginated list of languages.
     * Responds to URLs like: /index.php?controller=Language&action=index
     * Supports filtering: /index.php?controller=Language&action=index&filter[Name_like]=Eng%
     */
    public function index(Request $request): Response
    {
        // Get pagination parameters from the request, with sensible defaults.
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        // Parse the filter from the request query
        $filterParams = $request->getQuery('filter');
        $filter = $this->parseFilter($filterParams);

        // The service now returns a structured array with data and total count.
        $result = $this->service->getLanguages($filter, $page, $pageSize, $reload);

        return $this->json($result);
    }

    /**
     * Handles requests for a single language by its ID.
     * Responds to URLs like: /index.php?controller=Language&action=show&id=lang-uuid-123
     */
    public function show(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        // Create a filter to find the specific language by its ID.
        $filter = [['Id', '=', $id]];
        $result = $this->service->getLanguages($filter, 1, 1, ReloadMode::YES);

        return $this->json($result);
    }

    /**
     * Creates a new language.
     * Expects a POST request with a JSON body.
     * /index.php?controller=Language&action=store
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);

        if (!$data) {
            return $this->json(['error' => 'Invalid or empty JSON body.'], 400);
        }

        try {
            $newItem = $this->service->setLanguage($data);
            return $this->json($newItem, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create language.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing language.
     * Expects a POST/PUT request with a JSON body and an ID in the query string.
     * /index.php?controller=Language&action=update&id=lang-uuid-123
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID parameter and JSON body are required.'], 400);
        }

        try {
            $updatedItem = $this->service->putLanguage($id, $data);

            if (!$updatedItem) {
                return $this->json(['error' => 'Language not found.'], 404);
            }
            return $this->json($updatedItem, 200); // 200 OK
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update language.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a language by its ID.
     * /index.php?controller=Language&action=destroy&id=lang-uuid-123
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');

        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $success = $this->service->deleteLanguage($id);

            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete language.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}