<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\AppRelation;

class AppRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }
}