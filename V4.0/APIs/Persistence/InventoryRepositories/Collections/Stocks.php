<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Stock;

class Stocks extends Collectable
{
    public function __construct(array $collection, string $objectType = Stock::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Stock
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Stock ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Stock
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Stock ? $entity : null;
    }
}