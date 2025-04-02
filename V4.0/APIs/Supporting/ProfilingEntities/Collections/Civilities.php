<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Civility;

class Civilities extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Civility::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Civility ? $entity : null;
    }
}