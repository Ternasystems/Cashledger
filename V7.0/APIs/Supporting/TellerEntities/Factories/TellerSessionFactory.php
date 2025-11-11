<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Collection\TellerSessions;
use API_TellerEntities_Model\TellerSession;
use API_TellerRepositories\TellerSessionRepository;
use API_TellerRepositories\TellerTransactionRepository;
use API_TellerRepositories_Collection\TellerTransactions;
use TS_Exception\Classes\DomainException;

class TellerSessionFactory extends CollectableFactory
{
    private TellerFactory $factory;
    private Tellers $tellers;
    private TellerTransactionRepository $transactionRepository;
    private TellerTransactions $transactions;

    public function __construct(TellerSessionRepository $repository, TellerFactory $factory, TellerTransactionRepository $transactionRepository,
                                LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $factory;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->TellerId)->toArray();
            $this->factory->filter([['ID', 'in', $ids]]);
        }

        $this->factory->Create();
        $this->tellers = $this->factory->collectable();

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->transactionRepository->getBy([['SessionId', 'in', $ids]]);
        }
        $this->transactions = new TellerTransactions($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $sessions = [];
        if ($this->collection)
            $sessions = $this->collection->select(fn($n) => new TellerSession($n, $this->tellers[$n->TellerId], $this->transactions->where(fn($t) => $t->SessionId == $n->Id)))->toArray();

        $this->collectable = new TellerSessions($sessions);
    }
}