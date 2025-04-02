<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Country;

class Countries extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Country::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Country ? $entity : null;
    }
}