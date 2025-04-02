<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\City;

class Cities extends EntityCollectable
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