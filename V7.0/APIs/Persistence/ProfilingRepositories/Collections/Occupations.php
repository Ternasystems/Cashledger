<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Occupation;
use Closure;

class Occupations extends Collectable
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