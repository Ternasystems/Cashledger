<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Product;

class Products extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Product::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Product
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Product ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Product
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Product ? $entity : null;
    }
}