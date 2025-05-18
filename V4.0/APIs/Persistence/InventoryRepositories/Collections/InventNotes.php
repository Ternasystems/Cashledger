<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\InventNote;

class InventNotes extends Collectable
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