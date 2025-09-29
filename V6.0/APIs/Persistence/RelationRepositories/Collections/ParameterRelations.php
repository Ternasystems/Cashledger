<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\ParameterRelation;
use Closure;

class ParameterRelations extends Collectable
{
    /**
     * Returns the first Parameter in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ParameterRelation|null
     */
    public function first(?Closure $callback = null): ?ParameterRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof ParameterRelation ? $entity : null;
    }

    /**
     * Returns the last Parameter in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ParameterRelation|null
     */
    public function last(?Closure $callback = null): ?ParameterRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof ParameterRelation ? $entity : null;
    }
}