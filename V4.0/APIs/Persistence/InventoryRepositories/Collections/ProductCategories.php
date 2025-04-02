<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\ProductCategory;

class ProductCategories extends Collectable
{
    public function __construct(array $collection, string $objectType = ProductCategory::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ProductCategory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ProductCategory ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ProductCategory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ProductCategory ? $entity : null;
    }
}