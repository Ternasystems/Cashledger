<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Gender;

class Genders extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Gender::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
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