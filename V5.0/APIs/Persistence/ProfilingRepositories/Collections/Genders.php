<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Gender;

class Genders extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Gender ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Gender ? $entity : null;
    }
}