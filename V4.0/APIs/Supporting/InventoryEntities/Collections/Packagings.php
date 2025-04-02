<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Packaging;

class Packagings extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Packaging::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Packaging
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Packaging ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Packaging
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Packaging ? $entity : null;
    }
}