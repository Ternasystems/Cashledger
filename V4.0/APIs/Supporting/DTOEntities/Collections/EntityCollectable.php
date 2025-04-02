<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Entity;
use TS_Utility\Classes\AbstractCollectable;

class EntityCollectable extends AbstractCollectable
{
    public function __construct(array $collection, string $objectType = Entity::class, string $keySet = null)
    {
        $collectionArray = $collection;
        $collection = [];
        foreach ($collectionArray as $item)
            $collection[$item->It()->Id] = $item;

        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Entity
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Entity ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Entity
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Entity ? $entity : null;
    }
}