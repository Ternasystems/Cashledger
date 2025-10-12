<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Tracking;
use Closure;

class Trackings extends Collectable
{
    /**
     * Returns the first Tracking in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Tracking|null
     */
    public function first(?Closure $callback = null): ?Tracking
    {
        $entity = parent::first($callback);
        return $entity instanceof Tracking ? $entity : null;
    }

    /**
     * Returns the last Tracking in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Tracking|null
     */
    public function last(?Closure $callback = null): ?Tracking
    {
        $entity = parent::last($callback);
        return $entity instanceof Tracking ? $entity : null;
    }
}