<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Permission;

class Permissions extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Permission::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
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