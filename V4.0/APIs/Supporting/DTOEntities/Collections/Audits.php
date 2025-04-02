<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Audit;

class Audits extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Audit::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Audit ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Audit ? $entity : null;
    }
}