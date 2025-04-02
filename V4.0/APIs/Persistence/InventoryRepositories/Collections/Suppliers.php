<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Supplier;

class Suppliers extends Collectable
{
    public function __construct(array $collection, string $objectType = Supplier::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Supplier
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Supplier ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Supplier
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Supplier ? $entity : null;
    }
}