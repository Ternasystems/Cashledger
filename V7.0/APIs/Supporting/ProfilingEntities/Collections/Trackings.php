<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Tracking;
use Closure;

class Trackings extends EntityCollectable
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