<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Unit;

class Units extends Collectable
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