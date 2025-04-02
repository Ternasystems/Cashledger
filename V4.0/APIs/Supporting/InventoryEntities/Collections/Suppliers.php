<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Supplier;

class Suppliers extends EntityCollectable
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