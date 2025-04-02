<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Title;

class Titles extends Collectable
{
    public function __construct(array $collection, string $objectType = Title::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Title ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Title ? $entity : null;
    }
}