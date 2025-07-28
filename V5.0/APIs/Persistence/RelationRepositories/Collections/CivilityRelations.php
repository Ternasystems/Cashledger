<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\CivilityRelation;

class CivilityRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }
}