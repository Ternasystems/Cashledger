<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\OccupationRelation;
use Closure;

class OccupationRelations extends Collectable
{
    /**
     * Returns the first Occupation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return OccupationRelation|null
     */
    public function first(?Closure $callback = null): ?OccupationRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    /**
     * Returns the last Occupation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return OccupationRelation|null
     */
    public function last(?Closure $callback = null): ?OccupationRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof OccupationRelation ? $entity : null;
    }
}