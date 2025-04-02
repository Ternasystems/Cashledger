<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\City;

class Cities extends Collectable
{
    public function __construct(array $collection, string $objectType = City::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof City ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof City ? $entity : null;
    }
}