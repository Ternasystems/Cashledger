<?php

namespace API_Teller_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ITellerCashCountService;
use API_Teller_Contract\ITellerPaymentService;
use API_Teller_Contract\ITellerReceiptService;
use API_Teller_Contract\ITellerSessionService;
use API_Teller_Contract\ITellerTransactionService;
use API_Teller_Contract\ITellerTransferService;
use API_TellerEntities_Collection\TellerCashCounts;
use API_TellerEntities_Collection\TellerPayments;
use API_TellerEntities_Collection\TellerReceipts;
use API_TellerEntities_Collection\TellerSessions;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Collection\TellerTransfers;
use API_TellerEntities_Model\TellerCashCount;
use API_TellerEntities_Model\TellerPayment;
use API_TellerEntities_Model\TellerReceipt;
use API_TellerEntities_Model\TellerSession;
use API_TellerEntities_Model\TellerTransaction;
use API_TellerEntities_Model\TellerTransfer;
use Exception;

/**
 * This is the Facade class for TellerSession management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class TellerSessionFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected ITellerPaymentService $paymentService, protected ITellerReceiptService $receiptService, protected ITellerTransferService $transferService,
                                protected ITellerSessionService $sessionService, protected ITellerTransactionService $transactionService,
                                protected ITellerCashCountService $cashCountService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|TellerPayments|TellerPayment|TellerReceipts|TellerReceipt|
    TellerTransfers|TellerTransfer|TellerSessions|TellerSession|TellerTransactions|TellerTransaction|TellerCashCounts|TellerCashCount
    {
        return match ($resourceType) {
            'TellerPayment' => $this->paymentService->getTellerPayments($filter, $page, $pageSize, $reloadMode),
            'TellerReceipt' => $this->receiptService->getTellerReceipts($filter, $page, $pageSize, $reloadMode),
            'TellerTransfer' => $this->transferService->getTellerTransfers($filter, $page, $pageSize, $reloadMode),
            'TellerSession' => $this->sessionService->getTellerSessions($filter, $page, $pageSize, $reloadMode),
            'TellerTransaction' => $this->transactionService->getTellerTransactions($filter, $page, $pageSize, $reloadMode),
            'TellerCashCount' => $this->cashCountService->getTellerCashCounts($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for TellerSessionFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): TellerPayments|TellerPayment|TellerReceipts|TellerReceipt|TellerTransfers|TellerTransfer|TellerSessions|TellerSession|
    TellerTransactions|TellerTransaction|TellerCashCounts|TellerCashCount
    {
        return match ($resourceType) {
            'TellerPayment' => $this->paymentService->setTellerPayment($data),
            'TellerReceipt' => $this->receiptService->setTellerReceipt($data),
            'TellerTransfer' => $this->transferService->setTellerTransfer($data),
            'TellerSession' => $this->sessionService->setTellerSession($data),
            'TellerTransaction' => $this->transactionService->setTellerTransaction($data),
            'TellerCashCount' => $this->cashCountService->setTellerCashCount($data),
            default => throw new Exception("Invalid resource type for TellerSessionFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): mixed
    {
        return throw new Exception("Invalid or unsupported resource type for TellerSessionFacade 'put': $resourceType");
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for TellerSessionFacade 'delete': $resourceType");
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for TellerSessionFacade 'disable': $resourceType");
    }
}