<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\DTOBase;
use TS_Utility\Classes\AbstractCollectable;

class Collectable extends AbstractCollectable
{
    public function __construct(array $collection,string $objectType = DTOBase::class, string $keySet = null)
    {
        $collectionArray = $collection;
        $collection = [];
        foreach ($collectionArray as $item)
            $collection[$item->Id] = $item;

        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DTOBase
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DTOBase ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DTOBase
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DTOBase ? $entity : null;
    }
}