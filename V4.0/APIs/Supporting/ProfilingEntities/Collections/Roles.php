<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Role;

class Roles extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Role::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Role ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Role ? $entity : null;
    }
}