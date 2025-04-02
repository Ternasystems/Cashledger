<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Tracking;

class Trackings extends EntityCollectable
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