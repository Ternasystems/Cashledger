<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\StockAttributes;
use API_InventoryEntities_Model\StockAttribute;
use API_InventoryRepositories\ProductAttributeRepository;
use API_RelationRepositories\StockRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class StockAttributeFactory extends CollectableFactory
{
    protected StockRelationRepository $relations;
    public function __construct(ProductAttributeRepository $repository, StockRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
    {
        parent::__construct($repository, $_relationRepository);
        $this->relations = $_relations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new StockAttribute($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new StockAttributes($colArray);
    }

    public function Collectable(): ?StockAttributes
    {
        return $this->collectable;
    }

    public function Repository(): ProductAttributeRepository
    {
        return $this->repository;
    }
}