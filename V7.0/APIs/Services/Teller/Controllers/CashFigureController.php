<?php

namespace API_Teller_Controller;

use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ICashFigureService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CashFigureController extends BaseController
{
    protected ICashFigureService $service;

    public function __construct(ICashFigureService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of cash figures (denominations).
     * e.g., /?controller=CashFigure&action=index
     */
    public function index(Request $request): Response
    {
        try {
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = $this->service->getCashFigures($filter, $page, $pageSize, $reload);
            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve cash figures.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single cash figure by its ID.
     * e.g., /?controller=CashFigure&action=show&id=figure-uuid-123
     */
    public function show(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];
            $result = $this->service->getCashFigures($filter, 1, 1, ReloadMode::YES);
            return $this->json($result);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve cash figure.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new cash figure.
     * Expects a POST request with a JSON body.
     * e.g., /?controller=CashFigure&action=store
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = $this->service->SetCashFigure($data);
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create cash figure.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing cash figure.
     * e.g., /?controller=CashFigure&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $item = $this->service->PutCashFigure($id, $data);
            if (!$item) {
                return $this->json(['error' => 'Cash figure not found.'], 404);
            }
            return $this->json($item);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update cash figure.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a cash figure by its ID.
     * e.g., /?controller=CashFigure&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->DeleteCashFigure($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete cash figure.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
     */
    private function parseFilter(?array $filter): ?array
    {
        if (is_null($filter)) {
            return null;
        }

        $whereClause = [];
        $operatorMap = [
            '_like' => 'LIKE',
            '_gt' => '>',
            '_gte' => '>=',
            '_lt' => '<',
            '_lte' => '<=',
            '_neq' => '!='
        ];

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
}