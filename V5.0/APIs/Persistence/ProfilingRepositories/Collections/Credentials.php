<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Credential;

class Credentials extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
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