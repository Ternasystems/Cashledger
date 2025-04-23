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
    protected Stocks $stocks;
    protected DeliveryRelationRepository $deliveryRelations;

    /**
     * @throws Exception
     */
    public function __construct(DeliveryNoteRepository $repository, StockFactory $_stockFactory, DeliveryRelationRepository $_deliveryRelations)
    {
        parent::__construct($repository, null);
        $_stockFactory->Create();
        $this->stocks = $_stockFactory->Collectable();
        $this->deliveryRelations = $_deliveryRelations;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $deliveryRelations = $this->deliveryRelations->GetAll()->Where(fn($n) => $n->DeliveryId == $item->Id);
            $stockArray = [];
            foreach ($deliveryRelations as $deliveryRelation)
                $stockArray[] = $this->stocks->FirstOrDefault(fn($n) => $n->Id == $deliveryRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new DeliveryNote($item, $_stocks);
        }

        $this->collectable = new DeliveryNotes($colArray);
    }

    public function Collectable(): ?DeliveryNotes
    {
        return $this->collectable;
    }

    public function Repository(): DeliveryNoteRepository
    {
        return $this->repository;
    }
}