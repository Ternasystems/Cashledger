<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Suppliers;
use API_InventoryEntities_Model\Supplier;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;

interface ISupplierService
{
    public function GetProfiles(callable $predicate = null): Profile|Profiles|null;
    public function GetSuppliers(callable $predicate = null): Supplier|Suppliers|null;
    public function SetSupplier(object $model): void;
    public function PutSupplier(object $model): void;
    public function DeleteSupplier(string $id): void;
}