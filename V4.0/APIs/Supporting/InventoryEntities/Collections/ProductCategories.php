<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\ProductCategory;

class ProductCategories extends EntityCollectable
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