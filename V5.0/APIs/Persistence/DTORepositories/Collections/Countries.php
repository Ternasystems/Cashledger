<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Country;

class Countries extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Country ? $entity : null;
    }
}