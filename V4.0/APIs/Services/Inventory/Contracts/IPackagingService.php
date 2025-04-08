<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;

interface IPackagingService
{
    public function GetPackagings(callable $predicate = null): Packaging|Packagings|null;
    public function SetPackaging(object $model): void;
    public function PutPackaging(object $model): void;
    public function DeletePackaging(string $id): void;
}