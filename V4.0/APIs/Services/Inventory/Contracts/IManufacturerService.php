<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Manufacturers;
use API_InventoryEntities_Model\Manufacturer;

interface IManufacturerService
{
    public function GetManufacturers(callable $predicate = null): Manufacturer|Manufacturers|null;
    public function SetManufacturer(object $model): void;
    public function PutManufacturer(object $model): void;
    public function DeleteManufacturer(string $id): void;
}