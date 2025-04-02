<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Title;

class Titles extends EntityCollectable
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