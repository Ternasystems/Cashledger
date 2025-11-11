<?php

namespace API_Teller_Controller;

use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ITellerAuditService;
use API_Teller_Contract\ITellerReversalService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class TellerAuditController extends BaseController
{
    protected ITellerAuditService $auditService;
    protected ITellerReversalService $reversalService;

    public function __construct(
        ITellerAuditService $auditService,
        ITellerReversalService $reversalService
    ) {
        $this->auditService = $auditService;
        $this->reversalService = $reversalService;
    }

    /**
     * Gets a list of audits or reversals.
     * e.g., /?controller=TellerAudit&action=index
     * e.g., /?controller=TellerReversal&action=index&filter[SessionId]=...
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'TellerAudit');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = match ($controller) {
                'TellerAudit' => $this->auditService->getTellerAudits($filter, $page, $pageSize, $reload),
                'TellerReversal' => $this->reversalService->getTellerReversals($filter, $page, $pageSize, $reload),
                default => throw new Exception('Invalid controller context for index action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single audit or reversal by its ID.
     * e.g., /?controller=TellerAudit&action=show&id=audit-uuid-123
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'TellerAudit');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];

            $result = match ($controller) {
                'TellerAudit' => $this->auditService->getTellerAudits($filter, 1, 1, ReloadMode::YES),
                'TellerReversal' => $this->reversalService->getTellerReversals($filter, 1, 1, ReloadMode::YES),
                default => throw new Exception('Invalid controller context for show action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new audit or reversal.
     * e.g., /?controller=TellerAudit&action=store
     * e.g., /?controller=TellerReversal&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'TellerAudit');
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = match ($controller) {
                'TellerAudit' => $this->auditService->SetTellerAudit($data),
                'TellerReversal' => $this->reversalService->SetTellerReversal($data),
                default => throw new Exception('Invalid controller context for store action.'),
            };
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing resource.
     * (Note: Neither of these services support update)
     */
    public function update(Request $request): Response
    {
        return $this->json(['error' => 'This resource does not support the update action.'], 405); // 405 Method Not Allowed
    }

    /**
     * Deletes a resource.
     * (Note: Neither of these services support delete)
     */
    public function destroy(Request $request): Response
    {
        return $this->json(['error' => 'This resource does not support the destroy action.'], 405); // 405 Method Not Allowed
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