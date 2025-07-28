<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Continent;

class Continents extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Continent ? $entity : null;
    }
}