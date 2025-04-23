<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\Inventory;
use TS_Controller\Classes\BaseController;

class StockController extends BaseController
{
    protected IStockService $stockService;
    protected IInventoryService $inventoryService;

    public function __construct(IStockService $_stockService, IInventoryService $_inventoryService)
    {
        $this->stockService = $_stockService;
        $this->inventoryService = $_inventoryService;
    }

    public function Get(): Stocks
    {
        return $this->stockService->GetStocks();
    }

    public function GetById(int $id): ?Stocks
    {
        return $this->stockService->GetStocks(fn($n) => $n->Id == $id);
    }

    public function GetByProductId(int $productId): ?Stocks
    {
        return $this->stockService->GetStocks(fn($n) => $n->ProductId == $productId);
    }

    public function Set(object $stock): void
    {
        $this->stockService->SetStock($stock);
    }

    public function Put(object $stock): void
    {
        $this->stockService->PutStock($stock);
    }

    public function Delete(string $id): void
    {
        $this->stockService->DeleteStock($id);
    }

    public function GetInventories(): Inventories
    {
        return $this->inventoryService->GetInventories();
    }

    public function GetInventoryById(string $id): ?Inventory
    {
        return $this->inventoryService->GetInventories(fn($n) => $n->Id == $id);
    }

    public function GetInventoryByProductId(string $productId): ?Inventory
    {
        return $this->inventoryService->GetInventories(fn($n) => $n->ProductId == $productId);
    }
}