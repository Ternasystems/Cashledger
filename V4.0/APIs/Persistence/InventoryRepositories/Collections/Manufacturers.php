<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Manufacturer;

class Manufacturers extends Collectable
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