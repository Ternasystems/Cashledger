<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IWarehouseService;
use API_InventoryEntities_Collection\Warehouses;
use API_InventoryEntities_Model\Warehouse;
use TS_Controller\Classes\BaseController;

class WarehouseController extends BaseController
{
    protected IWarehouseService $service;

    public function __construct(IWarehouseService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Warehouses
    {
        return $this->service->GetWarehouses();
    }

    public function GetById(string $id): ?Warehouse
    {
        return $this->service->GetWarehouses(fn($n) => $n->It()->Id == $id);
    }

    public function Set(object $warehouse): void
    {
        $this->service->SetWarehouse($warehouse);
    }

    public function Put(object $warehouse): void
    {
        $this->service->PutWarehouse($warehouse);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteWarehouse($id);
    }
}