<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\InventNote;

class InventNotes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = InventNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventNote ? $entity : null;
    }
}