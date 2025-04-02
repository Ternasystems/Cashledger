<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Stock;

class Stocks extends EntityCollectable
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