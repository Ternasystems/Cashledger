<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Profile;
use Closure;

class Profiles extends Collectable
{
    /**
     * Returns the first Profile in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Profile|null
     */
    public function first(?Closure $callback = null): ?Profile
    {
        $entity = parent::first($callback);
        return $entity instanceof Profile ? $entity : null;
    }

    /**
     * Returns the last Profile in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Profile|null
     */
    public function last(?Closure $callback = null): ?Profile
    {
        $entity = parent::last($callback);
        return $entity instanceof Profile ? $entity : null;
    }
}