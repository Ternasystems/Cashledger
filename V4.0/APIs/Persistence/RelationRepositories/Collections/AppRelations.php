<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\AppRelation;

class AppRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = AppRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
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