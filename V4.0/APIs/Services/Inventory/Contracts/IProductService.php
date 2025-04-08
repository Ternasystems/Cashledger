<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\ProductCategories;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryEntities_Model\ProductCategory;

interface IProductService
{
    public function GetCategories(callable $predicate = null): ProductCategory|ProductCategories|null;
    public function SetCategory(object $model): void;
    public function PutCategory(object $model): void;
    public function DeleteCategory(string $id): void;
    public function GetAttributes(callable $predicate = null): ProductAttribute|ProductAttributes|null;
    public function SetAttribute(object $model): void;
    public function PutAttribute(object $model): void;
    public function DeleteAttribute(string $id): void;
    public function GetProducts(callable $predicate = null): Product|Products|null;
    public function SetProduct(object $model): void;
    public function PutProduct(object $model): void;
    public function DeleteProduct(string $id): void;
}