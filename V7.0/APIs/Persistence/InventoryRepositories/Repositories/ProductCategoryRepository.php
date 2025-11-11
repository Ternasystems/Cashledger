<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_InventoryRepositories_Collection\ProductCategories;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\ProductCategory;

/**
 * @extends Repository<ProductCategory, ProductCategories>
 */
class ProductCategoryRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context, ProductCategory::class, ProductCategories::class);
    }
}