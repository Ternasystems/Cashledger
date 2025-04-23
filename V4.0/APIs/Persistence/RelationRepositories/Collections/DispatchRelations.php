<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\DispatchRelation;

class DispatchRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = DispatchRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DispatchRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DispatchRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DispatchRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DispatchRelation ? $entity : null;
    }
}