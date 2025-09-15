<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\ContactRelation;
use Closure;

class ContactRelations extends Collectable
{
    /**
     * Returns the first Contact in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ContactRelation|null
     */
    public function first(?Closure $callback = null): ?ContactRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    /**
     * Returns the last Contact in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ContactRelation|null
     */
    public function last(?Closure $callback = null): ?ContactRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof ContactRelation ? $entity : null;
    }
}