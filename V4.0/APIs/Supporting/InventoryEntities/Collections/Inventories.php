<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Inventory;

class Inventories extends EntityCollectable
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