<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\City;
use Closure;

class Cities extends EntityCollectable
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