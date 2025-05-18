<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\InventRelation;

class InventRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = InventRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventRelation ? $entity : null;
    }
}