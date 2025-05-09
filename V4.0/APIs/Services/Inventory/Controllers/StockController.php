<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IDeliveryService;
use API_Inventory_Contract\IDispatchService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\DeliveryNotes;
use API_InventoryEntities_Collection\DispatchNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\DeliveryNote;
use API_InventoryEntities_Model\DispatchNote;
use API_InventoryEntities_Model\Stock;
use Exception;
use TS_Controller\Classes\BaseController;

class StockController extends BaseController
{
    protected IDeliveryService  $deliveryService;
    protected IDispatchService $dispatchService;
    protected IStockService $stockService;

    public function __construct(IDeliveryService $_deliveryService, IDispatchService $_dispatchService, IStockService $_stockService)
    {
        $this->deliveryService  = $_deliveryService;
        $this->dispatchService = $_dispatchService;
        $this->stockService = $_stockService;
    }

    public function Get(): Stocks
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
    public function GetByProductId(int $productId): ?Stocks
    {
        $collection = $this->stockService->GetStocks(fn($n) => $n->It()->ProductId == $productId);
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
}