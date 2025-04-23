<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\DeliveryRelation;

class DeliveryRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = DeliveryRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DeliveryRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DeliveryRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DeliveryRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DeliveryRelation ? $entity : null;
    }
}