<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Status;

class Statuses extends Collectable
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