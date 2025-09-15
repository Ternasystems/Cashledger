<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\GenderRelation;
use Closure;

class GenderRelations extends Collectable
{
    /**
     * Returns the first Gender in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return GenderRelation|null
     */
    public function first(?Closure $callback = null): ?GenderRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    /**
     * Returns the last Gender in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return GenderRelation|null
     */
    public function last(?Closure $callback = null): ?GenderRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof GenderRelation ? $entity : null;
    }
}