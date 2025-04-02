<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Product;

class Products extends Collectable
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