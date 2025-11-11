<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\CashRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\CashRelations;
use API_TellerEntities_Collection\TellerCashCounts;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Model\TellerCashCount;
use API_TellerRepositories\TellerCashCountRepository;
use TS_Exception\Classes\DomainException;

class TellerCashCountFactory extends CollectableFactory
{
    private TellerFactory $tellerFactory;
    private Tellers $tellers;
    private CashRelationRepository $relationRepository;
    private CashRelations $cashRelations;

    public function __construct(TellerCashCountRepository $repository, TellerFactory $tellerFactory, CashRelationRepository $relationRepository,
                                LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->tellerFactory = $tellerFactory;
        $this->relationRepository = $relationRepository;
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
            $this->tellerFactory->filter([['ID', 'in', $ids]]);
        }

        $this->tellerFactory->Create();
        $this->tellers = $this->tellerFactory->collectable();

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->relationRepository->getBy([['CountID', 'in', $ids]]);
        }
        $this->cashRelations = new CashRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $cashCounts = [];
        if ($this->collection)
            $cashCounts = $this->collection->select(fn($n) => new TellerCashCount($n, $this->tellers, $this->cashRelations->where(fn($t) => $t->CountId == $n->Id)))->toArray();

        $this->collectable = new TellerCashCounts($cashCounts);
    }
}