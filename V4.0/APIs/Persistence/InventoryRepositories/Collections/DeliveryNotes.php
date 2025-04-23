<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\DeliveryNote;

class DeliveryNotes extends Collectable
{
    public function __construct(array $collection, string $objectType = DeliveryNote::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DeliveryNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DeliveryNote ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DeliveryNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DeliveryNote ? $entity : null;
    }
}