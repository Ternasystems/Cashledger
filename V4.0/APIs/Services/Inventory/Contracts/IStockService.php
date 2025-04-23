<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Collection\Warehouses;
use API_InventoryEntities_Model\Packaging;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryEntities_Model\Stock;
use API_InventoryEntities_Model\Unit;
use API_InventoryEntities_Model\Warehouse;

interface IStockService
{
    public function GetAttributes(callable $predicate = null): ProductAttribute|ProductAttributes|null;
    public function GetProducts(callable $predicate = null): Product|Products|null;
    public function GetUnits(callable $predicate = null): Unit|Units|null;
    public function GetWarehouses(callable $predicate = null): Warehouse|Warehouses|null;
    public function GetPackagings(callable $predicate = null): Packaging|Packagings|null;
    public function GetStocks(callable $predicate = null): Stock|Stocks|null;
    public function SetStock(object $model): void;
    public function PutStock(object $model): void;
    public function DeleteStock(string $id): void;
}