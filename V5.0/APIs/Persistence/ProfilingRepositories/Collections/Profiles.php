<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Profile;

class Profiles extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Profile ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Profile ? $entity : null;
    }
}