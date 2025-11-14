<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IAuditService;
use API_Administration_Service\ReloadMode;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class AuditController extends BaseController
{
    protected IAuditService $service;

    public function __construct(IAuditService $service)
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
     * Gets a paginated list of audits.
     * Responds to URLs like: /index.php?controller=Audit&action=index
     * Supports filtering: /index.php?controller=Audit&action=index&filter[Name_like]=Eng%
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
        $result = $this->service->getAudits($filter, $page, $pageSize, $reload);

        return $this->json($result);
    }

    /**
     * Handles requests for a single audit by its ID.
     * Responds to URLs like: /index.php?controller=Audit&action=show&id=lang-uuid-123
     */
    public function show(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        // Create a filter to find the specific audit by its ID.
        $filter = [['Id', '=', $id]];
        $result = $this->service->getAudits($filter, 1, 1, ReloadMode::YES);

        return $this->json($result);
    }
}