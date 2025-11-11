<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\TellerReversals;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Model\TellerReversal;
use API_TellerRepositories\TellerReversalRepository;
use TS_Exception\Classes\DomainException;

class TellerReversalFactory extends CollectableFactory
{
    private TellerFactory $tellerFactory;
    private Tellers $tellers;
    private TellerTransactionFactory $transactionFactory;
    private TellerTransactions $transactions;

    public function __construct(TellerReversalRepository $repository, TellerTransactionFactory $transactionFactory, TellerFactory $tellerFactory,
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
            $tellers = $this->collection->select(fn($n) => $n->ReversedBy)->toArray();
            $approbators = $this->collection->select(fn($n) => $n->ApprovedBy)->toArray();
            $ids = array_merge($tellers, $approbators);
            $this->tellerFactory->filter([['ID', 'in', $ids]]);
        }

        $this->tellerFactory->Create();
        $this->tellers = $this->tellerFactory->collectable();

        $relations = [];
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
        $reversals = [];
        if ($this->collection)
            $reversals = $this->collection->select(fn($n) => new TellerReversal($n, $this->tellers, $this->transactions[$n->TransactionId]))->toArray();

        $this->collectable = new TellerReversals($reversals);
    }
}