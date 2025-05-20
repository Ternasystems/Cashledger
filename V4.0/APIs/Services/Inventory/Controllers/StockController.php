<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IDeliveryService;
use API_Inventory_Contract\IDispatchService;
use API_Inventory_Contract\IInventService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\DeliveryNotes;
use API_InventoryEntities_Collection\DispatchNotes;
use API_InventoryEntities_Collection\InventNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\DeliveryNote;
use API_InventoryEntities_Model\DispatchNote;
use API_InventoryEntities_Model\InventNote;
use API_InventoryEntities_Model\Stock;
use Exception;
use TS_Controller\Classes\BaseController;

class StockController extends BaseController
{
    protected IDeliveryService  $deliveryService;
    protected IDispatchService $dispatchService;
    protected IInventService $inventService;
    protected IStockService $stockService;

    public function __construct(IDeliveryService $_deliveryService, IDispatchService $_dispatchService, IInventService $_inventService, IStockService $_stockService)
    {
        $this->deliveryService  = $_deliveryService;
        $this->dispatchService = $_dispatchService;
        $this->inventService = $_inventService;
        $this->stockService = $_stockService;
    }

    public function Get(): ?Stocks
    {
        return $this->stockService->GetStocks();
    }

    public function GetById(int $id): ?Stock
    {
        return $this->stockService->GetStocks(fn($n) => $n->It()->Id == $id);
    }

    /**
     * @throws Exception
     */
    public function GetByProductId(string $productId): ?Stocks
    {
        $collection = $this->stockService->GetStocks(fn($n) => $n->It()->ProductId == $productId);
        if (empty($collection))
            return null;

        if ($collection instanceof Stock)
            $collection = new Stocks([$collection]);

        return $collection;
    }

    /**
     * @throws Exception
     */
    public function GetByWarehouseId(string $warehouseId): ?Stocks
    {
        $collection = $this->stockService->GetStocks(fn($n) => $n->It()->WarehouseId == $warehouseId);
        if (empty($collection))
            return null;

        if ($collection instanceof Stock)
            $collection = new Stocks([$collection]);

        return $collection;
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

    public function GetDeliveries(): ?DeliveryNotes
    {
        return $this->deliveryService->GetDeliveries();
    }

    public function GetDeliveryById(string $id): ?DeliveryNote
    {
        return $this->deliveryService->GetDeliveries(fn($n) => $n->It()->Id == $id);
    }

    public function GetDeliveryByNumber(string $batchNumber): ?DeliveryNote
    {
        return $this->deliveryService->GetDeliveries(fn($n) => $n->It()->BatchNumber == $batchNumber);
    }

    public function SetDelivery(object $delivery): void
    {
        $this->deliveryService->SetDelivery($delivery);
    }

    public function GetDispatches(): ?DispatchNotes
    {
        return $this->dispatchService->GetDispatches();
    }

    public function GetDispatchById(string $id): ?DispatchNote
    {
        return $this->dispatchService->GetDispatches(fn($n) => $n->It()->Id == $id);
    }

    public function GetDispatchByNumber(string $batchNumber): ?DispatchNote
    {
        return $this->dispatchService->GetDispatches(fn($n) => $n->It()->BatchNumber == $batchNumber);
    }

    public function SetDispatch(object $dispatch): void
    {
        $this->dispatchService->SetDispatch($dispatch);
    }

    public function GetInventories(): ?InventNotes
    {
        return $this->inventService->GetInventories();
    }

    public function GetInventoryById(string $id): ?InventNote
    {
        return $this->inventService->GetInventories(fn($n) => $n->It()->Id == $id);
    }

    public function GetInventByNumber(string $inventNumber): ?InventNote
    {
        return $this->inventService->GetInventories(fn($n) => $n->It()->InventNumber == $inventNumber);
    }

    public function SetInventories(object $inventory): void
    {
        $this->inventService->SetInventory($inventory);
    }
}