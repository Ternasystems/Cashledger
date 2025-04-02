<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Tracking;

class Trackings extends Collectable
{
    public function __construct(array $collection, string $objectType = Tracking::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }
}