<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Manufacturer;

class Manufacturers extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Manufacturer::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Manufacturer
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Manufacturer ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Manufacturer
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Manufacturer ? $entity : null;
    }
}