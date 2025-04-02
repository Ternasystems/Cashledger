<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Credential;

class Credentials extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Credential::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Credential ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Credential ? $entity : null;
    }
}