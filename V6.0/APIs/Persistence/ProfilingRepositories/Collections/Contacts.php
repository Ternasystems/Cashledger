<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Contact;
use Closure;

class Contacts extends Collectable
{
    /**
     * Returns the first Contact in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Contact|null
     */
    public function first(?Closure $callback = null): ?Contact
    {
        $entity = parent::first($callback);
        return $entity instanceof Contact ? $entity : null;
    }

    /**
     * Returns the last Contact in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Contact|null
     */
    public function last(?Closure $callback = null): ?Contact
    {
        $entity = parent::last($callback);
        return $entity instanceof Contact ? $entity : null;
    }
}