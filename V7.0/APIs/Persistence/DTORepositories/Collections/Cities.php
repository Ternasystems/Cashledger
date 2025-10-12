<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\City;
use Closure;

/**
 * A strongly-typed collection of City objects.
 */
class Cities extends Collectable
{
    /**
     * Returns the first City in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return City|null
     */
    public function first(?Closure $callback = null): ?City
    {
        $entity = parent::first($callback);
        return $entity instanceof City ? $entity : null;
    }

    /**
     * Returns the last City in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return City|null
     */
    public function last(?Closure $callback = null): ?City
    {
        $entity = parent::last($callback);
        return $entity instanceof City ? $entity : null;
    }
}