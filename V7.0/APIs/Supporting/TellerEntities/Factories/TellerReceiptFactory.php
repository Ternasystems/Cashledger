<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\TellerReceipts;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Model\TellerReceipt;
use API_TellerRepositories\TellerReceiptRepository;
use TS_Exception\Classes\DomainException;

class TellerReceiptFactory extends CollectableFactory
{
    private TellerFactory $tellerFactory;
    private Tellers $tellers;
    private TellerTransactionFactory $transactionFactory;
    private TellerTransactions $transactions;

    public function __construct(TellerReceiptRepository $repository, TellerTransactionFactory $transactionFactory, TellerFactory $tellerFactory,
                                LanguageRelationRepository $relationRepository)
    {
        parent::__construct($repository, $relationRepository);
        $this->tellerFactory = $tellerFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->PrintedBy)->toArray();
            $this->tellerFactory->filter([['ID', 'in', $ids]]);
        }

        $this->tellerFactory->Create();
        $this->tellers = $this->tellerFactory->collectable();

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->TransactionId)->toArray();
            $this->transactionFactory->filter([['ID', 'in', $ids]]);
        }

        $this->transactionFactory->Create();
        $this->transactions = $this->transactionFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $receipts = [];
        if ($this->collection)
            $receipts = $this->collection->select(fn($n) => new TellerReceipt($n, $this->tellers[$n->PrintedBy], $this->transactions[$n->TransactionId]))->toArray();

        $this->collectable = new TellerReceipts($receipts);
    }
}