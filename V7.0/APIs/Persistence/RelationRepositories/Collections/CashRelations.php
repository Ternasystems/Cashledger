<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\CashRelation;
use Closure;

class CashRelations extends Collectable
{
    /**
     * Returns the first Cash in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CashRelation|null
     */
    public function first(?Closure $callback = null): ?CashRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof CashRelation ? $entity : null;
    }

    /**
     * Returns the last Cash in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CashRelation|null
     */
    public function last(?Closure $callback = null): ?CashRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof CashRelation ? $entity : null;
    }
}