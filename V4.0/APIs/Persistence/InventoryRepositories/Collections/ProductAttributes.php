<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\ProductAttribute;

class ProductAttributes extends Collectable
{
    public function __construct(array $collection, string $objectType = ProductAttribute::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ProductAttribute
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ProductAttribute ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ProductAttribute
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ProductAttribute ? $entity : null;
    }
}