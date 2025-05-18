<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\ReturnNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\ReturnNote;
use API_InventoryRepositories\ReturnNoteRepository;
use API_RelationRepositories\ReturnRelationRepository;
use Exception;
use ReflectionException;

class ReturnNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected ReturnRelationRepository $returnRelations;

    /**
     * @throws Exception
     */
    public function __construct(ReturnNoteRepository $repository, StockFactory $_stockFactory, ReturnRelationRepository $_deliveryRelations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->returnRelations = $_deliveryRelations;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        if (is_null($collection))
            return;

        $colArray = [];
        $this->stockFactory->Create();
        $stocks = $this->stockFactory->Collectable();
        //
        foreach ($collection as $item) {
            $returnRelations = $this->returnRelations->GetAll()->Where(fn($n) => $n->DeliveryId == $item->Id);
            $stockArray = [];
            foreach ($returnRelations as $returnRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $returnRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new ReturnNote($item, $_stocks);
        }

        $this->collectable = new ReturnNotes($colArray);
    }

    public function Collectable(): ?ReturnNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): ReturnNoteRepository
    {
        return $this->repository;
    }
}