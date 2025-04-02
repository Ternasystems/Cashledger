<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Customers;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Collection\Suppliers;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Inventory;
use API_InventoryRepositories\InventoryRepository;
use API_InventoryRepositories\UnitRepository;
use API_RelationRepositories\InventoryRelationRepository;
use API_RelationRepositories_Collection\InventoryRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use Exception;

class InventoryFactory extends CollectableFactory
{
    protected Units $units;
    protected Stocks $stocks;
    protected Customers|Suppliers $partners;
    protected InventoryRelations $inventoryRelations;

    /**
     * @throws Exception
     */
    public function __construct(InventoryRepository $repository, StockFactory $_stockFactory, CustomerFactory|SupplierFactory $_partners, UnitRepository $_units,
                                InventoryRelationRepository $_inventoryRelations, ?LanguageRelations $relations)
    {
        parent::__construct($repository, null);
        $_stockFactory->Create();
        $this->stocks = $_stockFactory->Collectable();
        $_partners->Create();
        $this->partners = $_partners->Collectable();
        $factory = new CollectableFactory($_units, $relations);
        $factory->Create();
        $this->units = $factory->Collectable();
        $factory = new CollectableFactory($_inventoryRelations, $relations);
        $factory->Create();
        $this->inventoryRelations = $factory->Collectable();

    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $unit = $this->units->FirstOrDefault(fn($n) => $n->It()->Id == $item->UnitId);
            $stock = $this->stocks->FirstOrDefault(fn($n) => $n->It()->Id == $item->StockId);
            $partner = $this->partners->FirstOrDefault(fn($n) => $n->It()->Id == $item->PartnerId);
            $relations = $this->inventoryRelations->Where(fn($n) => $n->InventoryRelations()->Where(fn($t) => $t->InventoryId == $item->Id));
            $colArray[] = new Inventory($item, $unit, $stock, $partner, $relations);
        }

        $this->collectable = new Inventories($colArray);
    }

    public function Collectable(): ?Inventories
    {
        return $this->collectable;
    }

    public function Repository(): InventoryRepository
    {
        return $this->repository;
    }
}