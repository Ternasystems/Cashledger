<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\Stock;
use API_InventoryRepositories\PackagingRepository;
use API_InventoryRepositories\StockRepository;
use API_InventoryRepositories\UnitRepository;
use API_InventoryRepositories\WarehouseRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\StockRelationRepository;
use Exception;

class StockFactory extends CollectableFactory
{
    protected ProductFactory $productFactory;
    protected StockAttributeFactory $attributeFactory;
    protected ?StockRelationRepository $stockRelationRepository;
    protected UnitRepository $unitRepository;
    protected WarehouseRepository $warehouseRepository;
    private PackagingRepository $packagingRepository;
    protected LanguageRelationRepository $relations;

    /**
     * @throws Exception
     */
    public function __construct(StockRepository $repository, ProductFactory $_productFactory, StockAttributeFactory $_attributeFactory, UnitRepository $_unitRepository,
                                WarehouseRepository $_warehouseRepository, PackagingRepository $_packagingRepository, ?StockRelationRepository $_stockRelationRepository,
                                ?LanguageRelationRepository $_relations)
    {
        parent::__construct($repository, null);
        $this->productFactory = $_productFactory;
        $this->attributeFactory = $_attributeFactory;
        $this->unitRepository = $_unitRepository;
        $this->warehouseRepository = $_warehouseRepository;
        $this->packagingRepository = $_packagingRepository;
        $this->stockRelationRepository = $_stockRelationRepository;
        $this->relations = $_relations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        if (is_null($collection))
            return;

        $colArray = [];
        $this->productFactory->Create();
        $products = $this->productFactory->Collectable();
        $this->attributeFactory->Create();
        $attributes = $this->attributeFactory->Collectable();
        $factory = new CollectableFactory($this->unitRepository, $this->relations);
        $factory->Create();
        $units = $factory->Collectable();
        $factory = new CollectableFactory($this->warehouseRepository, null);
        $factory->Create();
        $warehouses = $factory->Collectable();
        $factory = new CollectableFactory($this->packagingRepository, $this->relations);
        $factory->Create();
        $packagings = $factory->Collectable();
        //
        foreach ($collection as $item) {
            $product = $products->FirstOrDefault(fn($n) => $n->It()->Id == $item->ProductId);
            $unit = $units->FirstOrDefault(fn($n) => $n->It()->Id == $item->UnitId);
            $warehouse = $warehouses->FirstOrDefault(fn($n) => $n->It()->Id == $item->WarehouseId);
            $packaging = $packagings->FirstOrDefault(fn($n) => $n->It()->Id == $item->PackagingId);
            $_attributes = $attributes?->Where(fn($n) => $n->StockRelations()?->Where(fn($t) => $t->StockId == $item->Id));
            $colArray[] = new Stock($item, $product, $unit, $warehouse, $packaging, $_attributes, $this->stockRelationRepository->GetAll());
        }

        $this->collectable = new Stocks($colArray);
    }

    public function Collectable(): ?Stocks
    {
        return $this->collectable ?? null;
    }

    public function Repository(): StockRepository
    {
        return $this->repository;
    }
}