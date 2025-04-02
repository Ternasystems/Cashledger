<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\InventoryRelation;

class InventoryRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = InventoryRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventoryRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventoryRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventoryRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventoryRelation ? $entity : null;
    }
}