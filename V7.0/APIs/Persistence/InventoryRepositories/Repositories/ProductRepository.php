<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_InventoryRepositories_Collection\Products;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Product;

/**
 * @extends Repository<Product, Products>
 */
class ProductRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context, Product::class, Products::class);
    }
}