<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\StockAttribute;

class StockAttributes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = StockAttribute::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StockAttribute
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof StockAttribute ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StockAttribute
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof StockAttribute ? $entity : null;
    }
}