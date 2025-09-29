<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Role;
use Closure;

class Roles extends EntityCollectable
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