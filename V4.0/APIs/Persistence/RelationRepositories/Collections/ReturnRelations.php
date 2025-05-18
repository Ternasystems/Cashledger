<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\ReturnRelation;

class ReturnRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = ReturnRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ReturnRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ReturnRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ReturnRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ReturnRelation ? $entity : null;
    }
}