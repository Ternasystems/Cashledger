<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\DeliveryNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\DeliveryNote;
use API_InventoryRepositories\DeliveryNoteRepository;
use API_RelationRepositories\DeliveryRelationRepository;
use Exception;
use ReflectionException;

class DeliveryNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected DeliveryRelationRepository $deliveryRelations;

    /**
     * @throws Exception
     */
    public function __construct(DeliveryNoteRepository $repository, StockFactory $_stockFactory, DeliveryRelationRepository $_deliveryRelations)
    {
        parent::__construct($repository, null);
       $this->stockFactory = $_stockFactory;
        $this->deliveryRelations = $_deliveryRelations;
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
            $deliveryRelations = $this->deliveryRelations->GetAll()->Where(fn($n) => $n->DeliveryId == $item->Id);
            $stockArray = [];
            foreach ($deliveryRelations as $deliveryRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $deliveryRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new DeliveryNote($item, $_stocks);
        }

        $this->collectable = new DeliveryNotes($colArray);
    }

    public function Collectable(): ?DeliveryNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): DeliveryNoteRepository
    {
        return $this->repository;
    }
}