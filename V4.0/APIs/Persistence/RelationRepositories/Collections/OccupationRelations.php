<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\OccupationRelation;

class OccupationRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = OccupationRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }
}