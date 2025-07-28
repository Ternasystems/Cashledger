<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\StatusRelation;

class StatusRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }
}