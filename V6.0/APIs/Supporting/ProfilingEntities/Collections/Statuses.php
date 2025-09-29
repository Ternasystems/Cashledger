<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Status;
use Closure;

class Statuses extends EntityCollectable
{
    /**
     * Returns the first Status in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Status|null
     */
    public function first(?Closure $callback = null): ?Status
    {
        $entity = parent::first($callback);
        return $entity instanceof Status ? $entity : null;
    }

    /**
     * Returns the last Status in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Status|null
     */
    public function last(?Closure $callback = null): ?Status
    {
        $entity = parent::last($callback);
        return $entity instanceof Status ? $entity : null;
    }
}