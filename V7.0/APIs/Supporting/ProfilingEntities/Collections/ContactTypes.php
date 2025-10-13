<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\ContactType;
use Closure;

class ContactTypes extends EntityCollectable
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