<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\AttributeRelation;

class AttributeRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = AttributeRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AttributeRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AttributeRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AttributeRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AttributeRelation ? $entity : null;
    }
}