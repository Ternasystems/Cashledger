<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;

interface IUnitService
{
    public function GetUnits(callable $predicate = null): Unit|Units|null;
    public function SetUnit(object $model): void;
    public function PutUnit(object $model): void;
    public function DeleteUnit(string $id): void;
}