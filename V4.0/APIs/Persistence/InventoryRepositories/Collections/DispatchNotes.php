<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\DispatchNote;

class DispatchNotes extends Collectable
{
    public function __construct(array $collection, string $objectType = DispatchNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DispatchNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DispatchNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DispatchNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DispatchNote ? $entity : null;
    }
}