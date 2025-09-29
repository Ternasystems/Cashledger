<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Credential;
use Closure;

class Credentials extends EntityCollectable
{
    /**
     * Returns the first Credential in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Credential|null
     */
    public function first(?Closure $callback = null): ?Credential
    {
        $entity = parent::first($callback);
        return $entity instanceof Credential ? $entity : null;
    }

    /**
     * Returns the last Credential in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Credential|null
     */
    public function last(?Closure $callback = null): ?Credential
    {
        $entity = parent::last($callback);
        return $entity instanceof Credential ? $entity : null;
    }
}