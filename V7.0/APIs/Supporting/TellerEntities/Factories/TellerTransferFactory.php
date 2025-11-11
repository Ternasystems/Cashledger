<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Collection\TellerTransfers;
use API_TellerEntities_Model\TellerTransfer;
use API_TellerRepositories\TellerTransferRepository;
use TS_Exception\Classes\DomainException;

class TellerTransferFactory extends CollectableFactory
{
    private TellerFactory $tellerFactory;
    private Tellers $tellers;
    private TellerTransactionFactory $transactionFactory;
    private TellerTransactions $transactions;

    function __construct(TellerTransferRepository $repository, TellerTransactionFactory $transactionFactory, TellerFactory $tellerFactory, LanguageRelationRepository $relationRepository)
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
            $tellerFroms = $this->collection->select(fn($n) => $n->TellerFrom)->toArray();
            $tellerTos = $this->collection->select(fn($n) => $n->TellerTo)->toArray();
            $approbators = $this->collection->select(fn($n) => $n->ApprovedBy)->toArray();
            $ids = array_merge($tellerFroms, $tellerTos, $approbators);
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
        $transfers = [];
        if ($this->collection)
            $transfers = $this->collection->select(fn($n) => new TellerTransfer($n, $this->tellers, $this->transactions[$n->TransactionId]))->toArray();

        $this->collectable = new TellerTransfers($transfers);
    }
}