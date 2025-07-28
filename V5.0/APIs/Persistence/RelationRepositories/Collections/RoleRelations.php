<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\RoleRelation;

class RoleRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof RoleRelation ? $entity : null;
    }
}