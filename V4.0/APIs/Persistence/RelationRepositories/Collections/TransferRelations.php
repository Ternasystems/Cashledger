<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\TransferRelation;

class TransferRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = TransferRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TransferRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TransferRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TransferRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TransferRelation ? $entity : null;
    }
}