<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IInventService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\InventNotes;
use API_InventoryEntities_Factory\InventNoteFactory;
use API_InventoryEntities_Model\InventNote;
use API_RelationRepositories\InventRelationRepository;
use API_RelationRepositories_Model\InventRelation;
use Exception;
use ReflectionException;

class InventService implements IInventService
{
    protected InventNoteFactory $inventFactory;
    protected InventRelationRepository $inventRelationRepository;
    protected IStockService $stockService;
    protected IInventoryService $inventoryService;

    public function __construct(InventNoteFactory $_inventFactory, InventRelationRepository $_inventRelationRepository, IStockService $_stockService,
                                IInventoryService $_inventoryService)
    {
        $this->inventFactory = $_inventFactory;
        $this->inventRelationRepository = $_inventRelationRepository;
        $this->stockService = $_stockService;
        $this->inventoryService = $_inventoryService;
    }

    /**
     * @throws ReflectionException
     */
    public function GetInventories(callable $predicate = null): InventNote|InventNotes|null
    {
        $this->inventFactory->Create();

        if (is_null($predicate))
            return $this->inventFactory->Collectable();

        $collection = $this->inventFactory->Collectable()->Where($predicate);

        return $collection->count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetInventory(object $model): void
    {
        // Set invent note
        $repository = $this->inventFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\InventNote::class, array($model->inventorynumber, null, $model->inventorydate->format('Y-m-d H:i:s'),
            $model->inventorydesc));
        $inventId = $repository->GetBy(fn($n) => $n->InventNumber == $model->inventorynumber)->FirstOrDefault()->Id;

        // Set stocks
        foreach ($model->stocks as $stock) {
            if ($stock->stockquantity != $stock->stockavailable){
                $stock->stockquantity = $stock->stockavailable - $stock->stockquantity;
                $this->stockService->DispatchStock($stock);
                $stockId = $stock->stockid;

                // Set invent relation
                $this->inventRelationRepository->Add(InventRelation::class, array($stockId, $inventId));
            }

            // Set inventory
            $stockInvent = $stock->stockinvent;
            $stockInvent->noteid = $inventId;
            $this->inventoryService->SetInventory($stockInvent);
        }
        $this->inventFactory->Create();
    }

    public function PutInventory(object $model): void
    {
        // TODO: Implement PutInventory() method.
    }

    public function DeleteInventory(string $id): void
    {
        // TODO: Implement DeleteInventory() method.
    }
}