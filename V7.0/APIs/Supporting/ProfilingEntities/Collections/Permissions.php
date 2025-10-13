<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Permission;
use Closure;

class Permissions extends EntityCollectable
{
    /**
     * Returns the first Permission in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Permission|null
     */
    public function first(?Closure $callback = null): ?Permission
    {
        $entity = parent::first($callback);
        return $entity instanceof Permission ? $entity : null;
    }

    /**
     * Returns the last Permission in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Permission|null
     */
    public function last(?Closure $callback = null): ?Permission
    {
        $entity = parent::last($callback);
        return $entity instanceof Permission ? $entity : null;
    }
}