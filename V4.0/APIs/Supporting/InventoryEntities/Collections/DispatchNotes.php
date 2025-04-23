<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\DispatchNote;

class DispatchNotes extends EntityCollectable
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