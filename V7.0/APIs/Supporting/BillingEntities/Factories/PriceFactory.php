<?php

namespace API_BillingEntities_Factory;

use API_BillingEntities_Collection\Prices;
use API_BillingEntities_Model\Price;
use API_BillingRepositories\PriceRepository;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\PriceRelationRepository;
use API_RelationRepositories_Collection\PriceRelations;
use TS_Exception\Classes\DomainException;

class PriceFactory extends CollectableFactory
{
    private PriceRelationRepository $priceRelationRepository;
    private PriceRelations $priceRelations;

    public function __construct(PriceRepository $repository, PriceRelationRepository $priceRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->priceRelationRepository = $priceRelationRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->priceRelationRepository->getBy([['PriceID', 'in', $ids]]);
        }
        $this->priceRelations = new PriceRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $prices = [];
        if ($this->collection)
            $prices = $this->collection->select(fn($n) => new Price($n, $this->priceRelations->where(fn($t) => $t->PriceId == $n->Id)))->toArray();

        $this->collectable = new Prices($prices);
    }
}