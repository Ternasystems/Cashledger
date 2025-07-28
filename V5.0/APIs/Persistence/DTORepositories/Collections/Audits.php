<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Audit;

class Audits extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
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