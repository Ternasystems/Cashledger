<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\TitleRelation;
use Closure;

class TitleRelations extends Collectable
{
    /**
     * Returns the first Title in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TitleRelation|null
     */
    public function first(?Closure $callback = null): ?TitleRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    /**
     * Returns the last Title in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TitleRelation|null
     */
    public function last(?Closure $callback = null): ?TitleRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof TitleRelation ? $entity : null;
    }
}