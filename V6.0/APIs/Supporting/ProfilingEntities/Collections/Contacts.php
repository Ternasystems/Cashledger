<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Contact;
use Closure;

class Contacts extends EntityCollectable
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