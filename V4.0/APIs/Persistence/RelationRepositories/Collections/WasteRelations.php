<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\WasteRelation;

class WasteRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = WasteRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?WasteRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof WasteRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?WasteRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof WasteRelation ? $entity : null;
    }
}