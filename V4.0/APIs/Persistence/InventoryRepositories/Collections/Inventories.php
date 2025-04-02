<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Inventory;

class Inventories extends Collectable
{
    public function __construct(array $collection, string $objectType = Inventory::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Inventory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Inventory ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Inventory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Inventory ? $entity : null;
    }
}