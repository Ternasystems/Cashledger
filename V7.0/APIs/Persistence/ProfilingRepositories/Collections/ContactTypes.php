<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\ContactType;
use Closure;

class ContactTypes extends Collectable
{
    /**
     * Returns the first ContactType in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ContactType|null
     */
    public function first(?Closure $callback = null): ?ContactType
    {
        $entity = parent::first($callback);
        return $entity instanceof ContactType ? $entity : null;
    }

    /**
     * Returns the last ContactType in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ContactType|null
     */
    public function last(?Closure $callback = null): ?ContactType
    {
        $entity = parent::last($callback);
        return $entity instanceof ContactType ? $entity : null;
    }
}