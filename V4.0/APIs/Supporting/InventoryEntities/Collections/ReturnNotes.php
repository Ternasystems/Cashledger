<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\ReturnNote;

class ReturnNotes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = ReturnNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ReturnNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ReturnNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ReturnNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ReturnNote ? $entity : null;
    }
}