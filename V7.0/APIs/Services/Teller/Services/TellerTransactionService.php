<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerTransactionService;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Factory\TellerTransactionFactory;
use API_TellerEntities_Model\TellerTransaction;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerTransactionService implements ITellerTransactionService
{
    protected TellerTransactionFactory $transactionFactory;
    protected TellerTransactions $transactions;

    public function __construct(TellerTransactionFactory $transactionFactory)
    {
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerTransactions(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerTransaction|TellerTransactions|null
    {
        if (!isset($this->transactions) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->transactionFactory->filter($filter, $pageSize, $offset);
            $this->transactionFactory->Create();
            $this->transactions = $this->transactionFactory->collectable();
        }

        if (count($this->transactions) === 0)
            return null;

        return $this->transactions->count() > 1 ? $this->transactions : $this->transactions->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerTransaction(array $data): TellerTransaction
    {
        $context = $this->transactionFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main transaction DTO
            $transaction = new \API_TellerRepositories_Model\TellerTransaction($data['transactionData']);
            $this->transactionFactory->repository()->add($transaction);

            // 2. Get the newly created transaction
            $transaction = $this->transactionFactory->repository()->last([['SessionId', '=', $data['transactionData']['SessionId']],
                ['CreatedBy', '=', $data['transactionData']['CreatedBy']]]);
            if (!$transaction)
                throw new TellerException('transaction_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerTransactions([['Id', '=', $transaction->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}