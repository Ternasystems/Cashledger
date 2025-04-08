<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Warehouse;

class Warehouses extends EntityCollectable
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