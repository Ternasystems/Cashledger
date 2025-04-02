<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\StockRelation;

class StockRelations extends Collectable
{
    public function __construct(array $collection, string $objectType = StockRelation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StockRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof StockRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StockRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof StockRelation ? $entity : null;
    }
}