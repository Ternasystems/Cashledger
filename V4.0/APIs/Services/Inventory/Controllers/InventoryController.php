<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Model\Inventory;
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

        $inventories = null;
        foreach ($collection as $item) {
            $elts = $this->inventoryService->GetInventories(fn($n) => $n->It()->StockId == $item->id);
            if ($elts instanceof Inventory)
                $inventories[] = $elts;
            else{
                foreach ($elts as $elt)
                    $inventories[] = $elt;
            }
        }
        return new Inventories($inventories);
    }

    /**
     * @throws Exception
     */
    public function GetByDeliveryId(string $deliveryId): ?Inventories
    {
        $collection = $this->inventoryService->GetInventories(fn($n) => $n->It()->NoteId == $deliveryId);
        if (empty($collection))
            return null;

        if ($collection instanceof Inventory)
            $collection = new Inventories([$collection]);

        return $collection;
    }
}