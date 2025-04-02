<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Unit;

class Units extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Unit::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Unit
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Unit ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Unit
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Unit ? $entity : null;
    }
}