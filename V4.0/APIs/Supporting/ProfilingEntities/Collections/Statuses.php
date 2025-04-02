<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Status;

class Statuses extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Status::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Status ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Status ? $entity : null;
    }
}