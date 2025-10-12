<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Credential;
use Closure;

class Credentials extends Collectable
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