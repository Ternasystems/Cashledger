<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\ContactType;

class ContactTypes extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ContactType
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ContactType
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ContactType ? $entity : null;
    }
}