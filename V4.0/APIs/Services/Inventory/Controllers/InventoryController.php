<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\Inventory;
use API_InventoryEntities_Model\Stock;
use API_InventoryRepositories_Model\InventoryType;
use Exception;
use TS_Controller\Classes\BaseController;

class InventoryController extends BaseController
{
    protected IInventoryService $inventoryService;
    protected IStockService $stockService;

    public function __construct(IInventoryService $_inventoryService, IStockService $_stockService)
    {
        $this->inventoryService = $_inventoryService;
        $this->stockService = $_stockService;
    }

    public function Get(): Inventory|Inventories|null
    {
        return $this->inventoryService->GetInventories();
    }

    public function GetById(string $id): ?Inventory
    {
        return $this->inventoryService->GetInventories(fn($n) => $n->It()->Id == $id);
    }

    /**
     * @throws Exception
     */
    public function GetByProductId(string $productId): ?Inventories
    {
        $collection = $this->stockService->GetStocks(fn($n) => $n->It()->ProductId == $productId);
        if (empty($collection))
            return null;

        if ($collection instanceof Stock)
            $collection = new Stocks([$collection]);

        $inventories = null;
        foreach ($collection as $stock) {
            $inventories = $this->inventoryService->GetInventories(fn($n) => $n->It()->StockId == $stock->It()->Id);
            if (empty($inventories))
                continue;

            if ($inventories instanceof Inventory)
                $inventories = new Inventories([$inventories]);
        }

        return $inventories;
    }

    /**
     * @throws Exception
     */
    public function GetByDeliveryId(string $deliveryId): ?Inventories
    {
        $collection = $this->inventoryService->GetInventories(fn($n) => $n->It()->NoteId == $deliveryId && $n->It()->InventoryType == InventoryType::IN);
        if (empty($collection))
            return null;

        if ($collection instanceof Inventory)
            $collection = new Inventories([$collection]);

        return $collection;
    }

    /**
     * @throws Exception
     */
    public function GetByDispatchId(string $dispatchId): ?Inventories
    {
        $collection = $this->inventoryService->GetInventories(fn($n) => $n->It()->NoteId == $dispatchId && $n->It()->InventoryType == InventoryType::OUT);
        if (empty($collection))
            return null;

        if ($collection instanceof Inventory)
            $collection = new Inventories([$collection]);

        return $collection;
    }
}