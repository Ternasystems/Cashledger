<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Status;
use Closure;

class Statuses extends Collectable
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