<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Customer;

class Customers extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Customer::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Customer
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Customer ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Customer
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Customer ? $entity : null;
    }
}