<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\AppRelation;
use Closure;

class AppRelations extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return AppRelation|null
     */
    public function first(?Closure $callback = null): ?AppRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof AppRelation ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return AppRelation|null
     */
    public function last(?Closure $callback = null): ?AppRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof AppRelation ? $entity : null;
    }
}