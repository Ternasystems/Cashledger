<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Packaging;

class Packagings extends Collectable
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