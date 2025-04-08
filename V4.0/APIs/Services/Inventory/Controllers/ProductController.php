<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IProductService;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\ProductCategories;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryEntities_Model\ProductCategory;
use TS_Controller\Classes\BaseController;

class ProductController extends BaseController
{
    protected IProductService $service;

    public function __construct(IProductService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Products
    {
        return $this->service->GetProducts();
    }

    public function GetById(string $id): ?Product
    {
        return $this->service->GetProducts(fn($n) => $n->It()->Id == $id);
    }

    public function Set(object $product): void
    {
        $this->service->SetProduct($product);
    }

    public function Put(object $product): void
    {
        $this->service->PutProduct($product);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteProduct($id);
    }

    public function GetCategories(): ?ProductCategories
    {
        return $this->service->GetCategories();
    }

    public function GetCategoryById(string $id): ?ProductCategory
    {
        return $this->service->GetCategories(fn($n) => $n->It()->Id == $id);
    }

    public function SetCategory(object $category): void
    {
        $this->service->SetCategory($category);
    }

    public function PutCategory(object $category): void
    {
        $this->service->PutCategory($category);
    }

    public function DeleteCategory(string $id): void
    {
        $this->service->DeleteCategory($id);
    }

    public function GetAttributes(): ?ProductAttributes
    {
        return $this->service->GetAttributes();
    }

    public function GetAttributeById(string $id): ?ProductAttribute
    {
        return $this->service->GetAttributes(fn($n) => $n->It()->Id == $id);
    }

    public function SetAttribute(object $category): void
    {
        $this->service->SetAttribute($category);
    }

    public function PutAttribute(object $category): void
    {
        $this->service->PutAttribute($category);
    }

    public function DeleteAttribute(string $id): void
    {
        $this->service->DeleteAttribute($id);
    }
}