<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\InventNotes;
use API_InventoryEntities_Model\InventNote;

interface IInventService
{
    public function GetInventories(callable $predicate = null): InventNote|InventNotes|null;
    public function SetInventory(object $model): void;
    public function PutInventory(object $model): void;
    public function DeleteInventory(string $id): void;
}