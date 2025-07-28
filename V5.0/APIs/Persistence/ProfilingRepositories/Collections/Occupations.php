<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Occupation;

class Occupations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
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