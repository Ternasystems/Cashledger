<?php

namespace API_InventoryRepositories_Context;

use API_DTORepositories_Context\Context;
use API_InventoryRepositories_Collection\Packagings;
use API_InventoryRepositories_Collection\ProductCategories;
use API_InventoryRepositories_Collection\Products;
use API_InventoryRepositories_Collection\Stocks;
use API_InventoryRepositories_Collection\Units;
use API_InventoryRepositories_Model\Packaging;
use API_InventoryRepositories_Model\Product;
use API_InventoryRepositories_Model\ProductCategory;
use API_InventoryRepositories_Model\Stock;
use API_InventoryRepositories_Model\Unit;

class InventoryContext extends Context
{
    // Table name properties, used by the TContext trait via the base Context.
    private string $packaging = 'cl_Packagings';
    private string $productcategory = 'cl_ProductCategories';
    private string $product = 'cl_Products';
    private string $stock = 'cl_Stocks';
    private string $unit = 'cl_Units';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'packaging' => Packaging::class,
            'productcategory' => ProductCategory::class,
            'product' => Product::class,
            'stock' => Stock::class,
            'unit' => Unit::class,
            'packagingcollection' => Packagings::class,
            'productcategorycollection' => ProductCategories::class,
            'productcollection' => Products::class,
            'stockcollection' => Stocks::class,
            'unitcollection' => Units::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'CategoryID' => 'CategoryId',
            'PackagingID' => 'PackagingId',
            'ProductID' => 'ProductId',
            'UnitID' => 'UnitId'
        ];
    }
}