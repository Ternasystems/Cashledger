<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\PriceRelation;
use Closure;

/**
 * A strongly-typed collection of PriceRelation objects.
 */
class PriceRelations extends Collectable
{
    /**
     * Returns the first PriceRelation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return PriceRelation|null
     */
    public function first(?Closure $callback = null): ?PriceRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof PriceRelation ? $entity : null;
    }

    /**
     * Returns the last PriceRelation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return PriceRelation|null
     */
    public function last(?Closure $callback = null): ?PriceRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof PriceRelation ? $entity : null;
    }
}