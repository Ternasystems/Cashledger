<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Model\Inventory;

interface IInventoryService
{
    public function GetInventories(callable $predicate = null): Inventory|Inventories|null;
    public function SetInventory(object $model): void;
    public function DeleteInventory(string $id): void;
}