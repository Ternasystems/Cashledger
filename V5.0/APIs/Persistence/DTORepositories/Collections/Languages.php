<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Language;

class Languages extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Language ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Language
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Language ? $entity : null;
    }
}