<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Occupation;
use Closure;

class Occupations extends EntityCollectable
{
    /**
     * Returns the first Occupation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Occupation|null
     */
    public function first(?Closure $callback = null): ?Occupation
    {
        $entity = parent::first($callback);
        return $entity instanceof Occupation ? $entity : null;
    }

    /**
     * Returns the last Occupation in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Occupation|null
     */
    public function last(?Closure $callback = null): ?Occupation
    {
        $entity = parent::last($callback);
        return $entity instanceof Occupation ? $entity : null;
    }
}