<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\TransferNote;

class TransferNotes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = TransferNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TransferNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TransferNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TransferNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TransferNote ? $entity : null;
    }
}