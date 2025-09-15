<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\LanguageRelation;
use Closure;

class LanguageRelations extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return LanguageRelation|null
     */
    public function first(?Closure $callback = null): ?LanguageRelation
    {
        $entity = parent::first($callback);
        return $entity instanceof LanguageRelation ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return LanguageRelation|null
     */
    public function last(?Closure $callback = null): ?LanguageRelation
    {
        $entity = parent::last($callback);
        return $entity instanceof LanguageRelation ? $entity : null;
    }
}