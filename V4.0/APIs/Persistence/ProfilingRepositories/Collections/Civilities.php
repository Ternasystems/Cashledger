<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Civility;

class Civilities extends Collectable
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