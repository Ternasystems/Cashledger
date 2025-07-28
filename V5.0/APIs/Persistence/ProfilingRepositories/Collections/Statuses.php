<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Status;

class Statuses extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
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