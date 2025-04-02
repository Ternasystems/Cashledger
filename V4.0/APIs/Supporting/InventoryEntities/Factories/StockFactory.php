<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Warehouses;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Collection\StockAttributes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Stock;
use API_InventoryRepositories\StockRepository;
use API_InventoryRepositories\UnitRepository;
use API_InventoryRepositories\WarehouseRepository;
use API_RelationRepositories_Collection\LanguageRelations;
use Exception;

class StockFactory extends CollectableFactory
{
    protected Units $units;
    protected Warehouses $warehouses;
    protected Products $products;
    protected StockAttributes $attributes;

    /**
     * @throws Exception
     */
    public function __construct(StockRepository $repository, ProductFactory $_productFactory, StockAttributeFactory $_attributeFactory, UnitRepository $_units, WarehouseRepository $_warehouses,
                                ?LanguageRelations $relations)
    {
        parent::__construct($repository, null);
        $_productFactory->Create();
        $this->products = $_productFactory->Collectable();
        $_attributeFactory->Create();
        $this->attributes = $_attributeFactory->Collectable();
        $factory = new CollectableFactory($_units, $relations);
        $factory->Create();
        $this->units = $factory->Collectable();
        $factory = new CollectableFactory($_warehouses, $relations);
        $factory->Create();
        $this->warehouses = $factory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $product = $this->products->FirstOrDefault(fn($n) => $n->It()->Id == $item->ProductId);
            $unit = $this->units->FirstOrDefault(fn($n) => $n->It()->Id == $item->UnitId);
            $warehouse = $this->warehouses->FirstOrDefault(fn($n) => $n->It()->Id == $item->WarehouseId);
            $_attributes = $this->attributes->Where(fn($n) => $n->StockRelations()->Where(fn($t) => $t->StockId == $item->Id));
            $colArray[] = new Stock($item, $product, $unit, $warehouse, $_attributes);
        }

        $this->collectable = new Stocks($colArray);
    }

    public function Collectable(): ?Stocks
    {
        return $this->collectable;
    }

    public function Repository(): StockRepository
    {
        return $this->repository;
    }
}