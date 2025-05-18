<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\WasteNote;

class WasteNotes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = WasteNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?WasteNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof WasteNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?WasteNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof WasteNote ? $entity : null;
    }
}