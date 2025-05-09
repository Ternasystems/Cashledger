<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IPackagingService;
use API_Inventory_Contract\IProductService;
use API_Inventory_Contract\IStockService;
use API_Inventory_Contract\IUnitService;
use API_Inventory_Contract\IWarehouseService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Collection\Warehouses;
use API_InventoryEntities_Factory\StockFactory;
use API_InventoryEntities_Model\Packaging;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\Stock;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryEntities_Model\Unit;
use API_InventoryEntities_Model\Warehouse;
use API_RelationRepositories\StockRelationRepository;
use API_RelationRepositories_Model\StockRelation;
use Exception;
use ReflectionException;

class StockService implements IStockService
{
    protected StockFactory $stockFactory;
    protected StockRelationRepository $stockRelationRepository;
    protected IProductService $productService;
    protected IUnitService $unitService;
    protected IWarehouseService $warehouseService;
    protected IPackagingService $packagingService;

    public function __construct(StockFactory $_stockFactory, StockRelationRepository $_stockRelationRepository, IProductService $_productService, IUnitService $_unitService,
                                IWarehouseService $_warehouseService, IPackagingService $_packagingService)
    {
        $this->stockFactory = $_stockFactory;
        $this->stockRelationRepository = $_stockRelationRepository;
        $this->productService = $_productService;
        $this->unitService = $_unitService;
        $this->warehouseService = $_warehouseService;
        $this->packagingService = $_packagingService;
    }

    public function GetAttributes(callable $predicate = null): ProductAttribute|ProductAttributes|null
    {
        return $this->productService->GetAttributes($predicate);
    }

    public function GetProducts(callable $predicate = null): Product|Products|null
    {
        return $this->productService->GetProducts($predicate);
    }

    public function GetUnits(callable $predicate = null): Unit|Units|null
    {
        return $this->unitService->GetUnits($predicate);
    }

    public function GetWarehouses(callable $predicate = null): Warehouse|Warehouses|null
    {
        return $this->warehouseService->GetWarehouses($predicate);
    }

    public function GetPackagings(callable $predicate = null): Packaging|Packagings|null
    {
        return $this->packagingService->GetPackagings($predicate);
    }

    /**
     * @throws Exception
     */
    public function GetStocks(callable $predicate = null): Stock|Stocks|null
    {
        $this->stockFactory->Create();

        if (is_null($predicate))
            return $this->stockFactory->Collectable();

        $collection = $this->stockFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetStock(object $model): void
    {
        $this->stockFactory->Create();
        $stock = $this->stockFactory->Collectable()?->FirstOrDefault(fn($n) => $n->It()->ProductId == $model->productid && $n->It()->WarehouseId == $model->warehouseid &&
        $n->It()->PackagingId == $model->packagingid && $n->It()->BatchNumber == $model->batchnumber);
        if (!is_null($stock)){
            $model->stockid = $stock->It()->Id;
            $model->stockquantity += $stock->It()->Quantity;
            $this->PutStock($model);
            return;
        }
        //
        $repository = $this->stockFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\Stock::class, array($model->productid, $model->unitid, $model->warehouseid, $model->packagingid,
            $model->batchnumber, $model->stockquantity, $model->unitcost));
        $this->stockFactory->Create();
        //
        if (!isset($model->attributes))
            return;

        $id = $this->stockFactory->Collectable()->FirstOrDefault(fn($n) => $n->ProductId == $model->productid && $n->WarehouseId == $model->warehouseid && $n->PackagingId ==
        $model->packagingid && $n->BatchNumber == $model->batchnumber)->It()->Id;
        foreach ($model->attributes as $key => $attribute)
            $this->stockRelationRepository->Add(StockRelation::class, array($key, $id, $attribute));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutStock(object $model): void
    {
        $repository = $this->stockFactory->Repository();
        $repository->Update(\API_InventoryRepositories_Model\Stock::class, array($model->stockid, $model->warehouseid, $model->packagingid, $model->batchnumber,
            $model->stockquantity, $model->unitcost, $model->desc ?? null));
        //
        $this->stockFactory->Create();
        //
        if (!is_null($this->stockRelationRepository->GetAll())){
            foreach ($this->stockRelationRepository->GetBy(fn($n) => $n->StockId == $model->stockid) as $stockRelation)
                $this->stockRelationRepository->Remove(StockRelation::class, array($stockRelation->Id));
        }
        //
        if (!isset($model->attributes))
            return;

        foreach ($model->attributes as $key => $attribute)
            $this->stockRelationRepository->Add(StockRelation::class, array($key, $model->stockid, $attribute));
    }

    /**
     * @throws Exception
     */
    public function DispatchStock(object $model): void
    {
        $this->stockFactory->Create();
        $repository = $this->stockFactory->Repository();
        $stock = $this->stockFactory->Collectable()?->FirstOrDefault(fn($n) => $n->It()->Id == $model->stockid);
        $stock->It()->Quantity -= $model->stockquantity;
        $repository->UpdateQuantity($stock->It()->Id, $stock->It()->Quantity);
        $this->stockFactory->Create();
    }

    /**
     * @throws Exception
     */
    public function DeleteStock(string $id): void
    {
        $this->stockFactory->Create();
        $attributes = $this->stockFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $id)->StockAttributes();
        foreach ($attributes as $attribute){
            $stockRelations = $attribute->StockRelations()->Where(fn($n) => $n->StockId == $id);
            foreach ($stockRelations as $stockRelation)
                $this->stockRelationRepository->Remove(StockRelation::class, array($stockRelation->Id));
        }
        //
        $repository = $this->stockFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\Stock::class, array($id));
    }
}