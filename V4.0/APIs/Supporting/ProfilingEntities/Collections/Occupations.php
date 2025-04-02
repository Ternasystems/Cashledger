<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Occupation;

class Occupations extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Occupation::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }
}