<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Warehouse;

class Warehouses extends Collectable
{
    public function __construct(array $collection, string $objectType = Warehouse::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Warehouse
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Warehouse ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Warehouse
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Warehouse ? $entity : null;
    }
}