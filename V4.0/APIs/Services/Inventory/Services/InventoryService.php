<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\IInventoryService;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Factory\InventoryFactory;
use API_InventoryEntities_Model\Inventory;
use API_RelationRepositories\InventoryRelationRepository;
use API_RelationRepositories_Model\InventoryRelation;
use Exception;
use ReflectionException;

class InventoryService implements IInventoryService
{
    protected InventoryFactory $inventoryFactory;
    protected InventoryRelationRepository $inventoryRelations;

    public function __construct(InventoryFactory $_inventoryFactory, InventoryRelationRepository $_inventoryRelations)
    {
        $this->inventoryFactory = $_inventoryFactory;
        $this->inventoryRelations = $_inventoryRelations;
    }

    /**
     * @throws Exception
     */
    public function GetInventories(callable $predicate = null): Inventory|Inventories|null
    {
        $this->inventoryFactory->Create();

        if (is_null($predicate))
            return $this->inventoryFactory->Collectable();

        $collection = $this->inventoryFactory->Collectable()->Where($predicate);

        if ($collection->count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetInventory(object $model): void
    {
        $repository = $this->inventoryFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\Inventory::class, array($model->noteid, $model->stockid, $model->unitid, $model->partnerid, $model->inventorytype,
            $model->quantity, $model->unitcost, $model->inventdesc ?? null));
        $id = $repository->LastOrDefault(fn($n) => $n->StockId == $model->stockid)->Id;
        //
        $this->inventoryRelations->Add(InventoryRelation::class, array($id, $model->credentialid));
        $this->inventoryFactory->Create();
    }

    /**
     * @throws Exception
     */
    public function DeleteInventory(string $id): void
    {
        $this->inventoryFactory->Create();
        $relations = $this->inventoryFactory->Collectable()->FirstOrDefault(fn($n) => $n->Id == $id)->InventoryRelations();
        foreach ($relations as $relation)
            $this->inventoryRelations->Remove(InventoryRelation::class, array($relation->Id));
        //
        $repository = $this->inventoryFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\Inventory::class, array($id));
    }
}