<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Role;
use Closure;

class Roles extends Collectable
{
    /**
     * Returns the first Role in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Role|null
     */
    public function first(?Closure $callback = null): ?Role
    {
        $entity = parent::first($callback);
        return $entity instanceof Role ? $entity : null;
    }

    /**
     * Returns the last Role in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Role|null
     */
    public function last(?Closure $callback = null): ?Role
    {
        $entity = parent::last($callback);
        return $entity instanceof Role ? $entity : null;
    }
}