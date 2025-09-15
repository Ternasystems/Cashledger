<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\RoleRelation;
use Closure;

class RoleRelations extends Collectable
{
    /**
     * Returns the first Role in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return RoleRelation|null
     */
    public function first(?Closure $callback = null): ?RoleRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    /**
     * Returns the last Role in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return RoleRelation|null
     */
    public function last(?Closure $callback = null): ?RoleRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof RoleRelation ? $entity : null;
    }
}