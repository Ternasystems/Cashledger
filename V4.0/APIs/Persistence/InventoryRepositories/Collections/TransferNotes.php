<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\TransferNote;

class TransferNotes extends Collectable
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