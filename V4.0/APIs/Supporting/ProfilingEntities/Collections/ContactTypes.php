<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\ContactType;

class ContactTypes extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = ContactType::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
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