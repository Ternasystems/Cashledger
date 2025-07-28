<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\ContactRelation;

class ContactRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }
}