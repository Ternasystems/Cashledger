<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\App;

class Apps extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = App::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof App ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof App ? $entity : null;
    }
}