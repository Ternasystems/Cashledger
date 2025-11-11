<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerReceiptService;
use API_TellerEntities_Collection\TellerReceipts;
use API_TellerEntities_Factory\TellerReceiptFactory;
use API_TellerEntities_Model\TellerReceipt;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerReceiptService implements ITellerReceiptService
{
    protected TellerReceiptFactory $receiptFactory;
    protected TellerReceipts $receipts;

    public function __construct(TellerReceiptFactory $receiptFactory)
    {
        $this->receiptFactory = $receiptFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerReceipts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerReceipt|TellerReceipts|null
    {
        if (!isset($this->receipts) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->receiptFactory->filter($filter, $pageSize, $offset);
            $this->receiptFactory->Create();
            $this->receipts = $this->receiptFactory->collectable();
        }

        if (count($this->receipts) === 0)
            return null;

        return $this->receipts->count() > 1 ? $this->receipts : $this->receipts->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerReceipt(array $data): TellerReceipt
    {
        $context = $this->receiptFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main receipt DTO
            $receipt = new \API_TellerRepositories_Model\TellerReceipt($data['receiptData']);
            $this->receiptFactory->repository()->add($receipt);

            // 2. Get the newly created receipt
            $receipt = $this->receiptFactory->repository()->first([['TransactionId', '=', $data['receiptData']['TransactionId']]]);
            if (!$receipt)
                throw new TellerException('receipt_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerReceipts([['Id', '=', $receipt->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}