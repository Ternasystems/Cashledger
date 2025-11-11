<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerTransferService;
use API_TellerEntities_Collection\TellerTransfers;
use API_TellerEntities_Factory\TellerTransferFactory;
use API_TellerEntities_Model\TellerTransfer;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerTransferService implements ITellerTransferService
{
    protected TellerTransferFactory $transferFactory;
    protected TellerTransfers $transfers;

    public function __construct(TellerTransferFactory $transferFactory)
    {
        $this->transferFactory = $transferFactory;
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerTransfers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerTransfer|TellerTransfers|null
    {
        if (!isset($this->transfers) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->transferFactory->filter($filter, $pageSize, $offset);
            $this->transferFactory->Create();
            $this->transfers = $this->transferFactory->collectable();
        }

        if (count($this->transfers) === 0)
            return null;

        return $this->transfers->count() > 1 ? $this->transfers : $this->transfers->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerTransfer(array $data): TellerTransfer
    {
        $context = $this->transferFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main transfer DTO
            $transfer = new \API_TellerRepositories_Model\TellerTransfer($data['transferData']);
            $this->transferFactory->repository()->add($transfer);

            // 2. Get the newly created transfer
            $transfer = $this->transferFactory->repository()->first([['TransactionId', '=', $data['transferData']['TransactionId']]]);
            if (!$transfer)
                throw new TellerException('transfer_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerTransfers([['Id', '=', $transfer->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}