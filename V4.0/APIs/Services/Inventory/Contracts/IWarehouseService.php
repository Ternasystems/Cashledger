<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Warehouses;
use API_InventoryEntities_Model\Warehouse;

interface IWarehouseService
{
    public function GetWarehouses(callable $predicate = null): Warehouse|Warehouses|null;
    public function SetWarehouse(object $model): void;
    public function PutWarehouse(object $model): void;
    public function DeleteWarehouse(string $id): void;
}