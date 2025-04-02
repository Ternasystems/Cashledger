<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\GenderRelation;

class GenderRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = GenderRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }
}