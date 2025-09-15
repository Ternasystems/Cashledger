<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Continent;
use Closure;

/**
 * A strongly-typed collection of Continent objects.
 */
class Continents extends Collectable
{
    /**
     * Returns the first Continent in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Continent|null
     */
    public function first(?Closure $callback = null): ?Continent
    {
        $entity = parent::first($callback);
        return $entity instanceof Continent ? $entity : null;
    }

    /**
     * Returns the last Continent in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Continent|null
     */
    public function last(?Closure $callback = null): ?Continent
    {
        $entity = parent::last($callback);
        return $entity instanceof Continent ? $entity : null;
    }
}