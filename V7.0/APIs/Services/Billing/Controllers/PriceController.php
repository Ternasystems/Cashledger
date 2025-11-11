<?php

namespace API_Billing_Controller;

use API_Administration_Service\ReloadMode;
use API_Billing_Contract\IPriceService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class PriceController extends BaseController
{
    protected IPriceService $service;

    public function __construct(IPriceService $service)
    {
        $this->service = $service;
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
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
     * Gets a paginated list of prices.
     * Supports filtering via query string.
     * e.g., /?controller=Price&action=index&filter[ProductId]=...
     */
    public function index(Request $request): Response
    {
        try {
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = $this->service->getPrices($filter, $page, $pageSize, $reload);
            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve prices.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single price by its ID.
     * e.g., /?controller=Price&action=show&id=price-uuid-123
     */
    public function show(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];
            $result = $this->service->getPrices($filter, 1, 1, ReloadMode::YES);
            return $this->json($result);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve price.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new price.
     * Expects a POST request with a JSON body.
     * e.g., /?controller=Price&action=store
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = $this->service->setPrice($data);
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create price.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing price.
     * Expects a POST request with a JSON body and an ID in the query string.
     * e.g., /?controller=Price&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $item = $this->service->putPrice($id, $data);
            if (!$item) {
                return $this->json(['error' => 'Price not found.'], 404);
            }
            return $this->json($item);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update price.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a price by its ID.
     * e.g., /?controller=Price&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->deletePrice($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete price.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}