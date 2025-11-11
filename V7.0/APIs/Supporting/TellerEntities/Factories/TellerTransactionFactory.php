<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Model\TellerTransaction;
use API_TellerRepositories\TellerTransactionRepository;
use TS_Exception\Classes\DomainException;

class TellerTransactionFactory extends CollectableFactory
{
    private TellerFactory $factory;
    private Tellers $tellers;

    public function __construct(TellerTransactionRepository $repository, TellerFactory $factory, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $factory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $tellers = $this->collection->select(fn($n) => $n->CreatedBy)->toArray();
            $approbators = $this->collection->select(fn($n) => $n->ApprovedBy)->toArray();
            $ids = array_merge($tellers, $approbators);
            $this->factory->filter([['ID', 'in', $ids]]);
        }

        $this->factory->Create();
        $this->tellers = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $transactions = [];
        if ($this->collection)
            $transactions = $this->collection->select(fn($n) => new TellerTransaction($n,
                $this->tellers->where(fn($t) => $t->it()->Id == $n->CreatedBy || $t->it()->Id == $n->ApprovedBy)))->toArray();

        $this->collectable = new Tellers($transactions);
    }
}