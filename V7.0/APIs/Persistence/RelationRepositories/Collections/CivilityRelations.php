<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\CivilityRelation;
use Closure;

class CivilityRelations extends Collectable
{
    /**
     * Returns the first Civility in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CivilityRelation|null
     */
    public function first(?Closure $callback = null): ?CivilityRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    /**
     * Returns the last Civility in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CivilityRelation|null
     */
    public function last(?Closure $callback = null): ?CivilityRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof CivilityRelation ? $entity : null;
    }
}