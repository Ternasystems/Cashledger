<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Continent;
use Closure;

class Continents extends EntityCollectable
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