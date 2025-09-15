<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\StatusRelation;
use Closure;

class StatusRelations extends Collectable
{
    /**
     * Returns the first Status in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return StatusRelation|null
     */
    public function first(?Closure $callback = null): ?StatusRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    /**
     * Returns the last Status in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return StatusRelation|null
     */
    public function last(?Closure $callback = null): ?StatusRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof StatusRelation ? $entity : null;
    }
}