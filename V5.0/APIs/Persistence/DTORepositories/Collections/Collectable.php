<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\DTOBase;
use TS_Domain\Classes\AbstractCollectable;

class Collectable extends AbstractCollectable
{
    public function __construct(array $collection)
    {
        $collectionArray = $collection;
        $collection = [];
        foreach ($collectionArray as $item)
            $collection[$item->Id] = $item;

        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DTOBase
    {
        $entity = parent::first($predicate);
        return $entity instanceof DTOBase ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DTOBase
    {
        $entity = parent::last($predicate);
        return $entity instanceof DTOBase ? $entity : null;
    }
}