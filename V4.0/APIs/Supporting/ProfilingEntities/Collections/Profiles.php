<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Profile;

class Profiles extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Profile::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
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