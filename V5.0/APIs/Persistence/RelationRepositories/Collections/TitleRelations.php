<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\TitleRelation;

class TitleRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TitleRelation ? $entity : null;
    }
}