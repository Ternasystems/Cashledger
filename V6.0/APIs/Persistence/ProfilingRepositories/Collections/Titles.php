<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Title;
use Closure;

class Titles extends Collectable
{
    /**
     * Returns the first Title in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Title|null
     */
    public function first(?Closure $callback = null): ?Title
    {
        $entity = parent::first($callback);
        return $entity instanceof Title ? $entity : null;
    }

    /**
     * Returns the last Title in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Title|null
     */
    public function last(?Closure $callback = null): ?Title
    {
        $entity = parent::last($callback);
        return $entity instanceof Title ? $entity : null;
    }
}