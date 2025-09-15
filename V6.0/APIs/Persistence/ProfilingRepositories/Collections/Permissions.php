<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Permission;
use Closure;

class Permissions extends Collectable
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