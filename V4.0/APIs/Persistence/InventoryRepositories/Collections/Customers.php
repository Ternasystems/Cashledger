<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Customer;

class Customers extends Collectable
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