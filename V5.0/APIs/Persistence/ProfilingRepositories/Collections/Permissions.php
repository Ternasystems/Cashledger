<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Permission;

class Permissions extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Permission ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Permission ? $entity : null;
    }
}