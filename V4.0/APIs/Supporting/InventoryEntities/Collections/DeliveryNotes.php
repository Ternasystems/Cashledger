<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\DeliveryNote;

class DeliveryNotes extends EntityCollectable
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