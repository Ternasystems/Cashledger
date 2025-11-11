<?php

namespace API_Teller_Controller;

use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ITellerCashCountService;
use API_Teller_Contract\ITellerPaymentService;
use API_Teller_Contract\ITellerReceiptService;
use API_Teller_Contract\ITellerSessionService;
use API_Teller_Contract\ITellerTransactionService;
use API_Teller_Contract\ITellerTransferService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class TellerSessionController extends BaseController
{
    protected ITellerSessionService $sessionService;
    protected ITellerTransactionService $transactionService;
    protected ITellerPaymentService $paymentService;
    protected ITellerReceiptService $receiptService;
    protected ITellerTransferService $transferService;
    protected ITellerCashCountService $cashCountService;

    public function __construct(
        ITellerSessionService $sessionService,
        ITellerTransactionService $transactionService,
        ITellerPaymentService $paymentService,
        ITellerReceiptService $receiptService,
        ITellerTransferService $transferService,
        ITellerCashCountService $cashCountService
    ) {
        $this->sessionService = $sessionService;
        $this->transactionService = $transactionService;
        $this->paymentService = $paymentService;
        $this->receiptService = $receiptService;
        $this->transferService = $transferService;
        $this->cashCountService = $cashCountService;
    }

    /**
     * Gets a list of resources related to a teller's session.
     * e.g., /?controller=TellerSession&action=index
     * e.g., /?controller=TellerTransaction&action=index&filter[SessionId]=...
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'TellerSession');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = match ($controller) {
                'TellerSession' => $this->sessionService->getTellerSessions($filter, $page, $pageSize, $reload),
                'TellerTransaction' => $this->transactionService->getTellerTransactions($filter, $page, $pageSize, $reload),
                'TellerPayment' => $this->paymentService->getTellerPayments($filter, $page, $pageSize, $reload),
                'TellerReceipt' => $this->receiptService->getTellerReceipts($filter, $page, $pageSize, $reload),
                'TellerTransfer' => $this->transferService->getTellerTransfers($filter, $page, $pageSize, $reload),
                'TellerCashCount' => $this->cashCountService->getTellerCashCounts($filter, $page, $pageSize, $reload),
                default => throw new Exception('Invalid controller context for index action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single session-related resource by its ID.
     * e.g., /?controller=TellerSession&action=show&id=session-uuid-123
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'TellerSession');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];

            $result = match ($controller) {
                'TellerSession' => $this->sessionService->getTellerSessions($filter, 1, 1, ReloadMode::YES),
                'TellerTransaction' => $this->transactionService->getTellerTransactions($filter, 1, 1, ReloadMode::YES),
                'TellerPayment' => $this->paymentService->getTellerPayments($filter, 1, 1, ReloadMode::YES),
                'TellerReceipt' => $this->receiptService->getTellerReceipts($filter, 1, 1, ReloadMode::YES),
                'TellerTransfer' => $this->transferService->getTellerTransfers($filter, 1, 1, ReloadMode::YES),
                'TellerCashCount' => $this->cashCountService->getTellerCashCounts($filter, 1, 1, ReloadMode::YES),
                default => throw new Exception('Invalid controller context for show action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new session-related resource.
     * e.g., /?controller=TellerSession&action=store (Starts a new session)
     * e.g., /?controller=TellerPayment&action=store (Makes a payment)
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'TellerSession');
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = match ($controller) {
                'TellerSession' => $this->sessionService->SetTellerSession($data),
                'TellerTransaction' => $this->transactionService->SetTellerTransaction($data),
                'TellerPayment' => $this->paymentService->SetTellerPayment($data),
                'TellerReceipt' => $this->receiptService->SetTellerReceipt($data),
                'TellerTransfer' => $this->transferService->SetTellerTransfer($data),
                'TellerCashCount' => $this->cashCountService->SetTellerCashCount($data),
                default => throw new Exception('Invalid controller context for store action.'),
            };
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing resource.
     * (Note: None of these services support update)
     */
    public function update(Request $request): Response
    {
        return $this->json(['error' => 'This resource does not support the update action.'], 405); // 405 Method Not Allowed
    }

    /**
     * Deletes a resource.
     * (Note: None of these services support delete)
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