<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\OccupationRelation;

class OccupationRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
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