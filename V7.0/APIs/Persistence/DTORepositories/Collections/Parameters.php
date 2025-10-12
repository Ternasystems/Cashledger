<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Parameter;
use Closure;

/**
 * A strongly-typed collection of Parameter objects.
 */
class Parameters extends Collectable
{
    /**
     * Returns the first Parameter in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Parameter|null
     */
    public function first(?Closure $callback = null): ?Parameter
    {
        $entity = parent::first($callback);
        return $entity instanceof Parameter ? $entity : null;
    }

    /**
     * Returns the last Parameter in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Parameter|null
     */
    public function last(?Closure $callback = null): ?Parameter
    {
        $entity = parent::last($callback);
        return $entity instanceof Parameter ? $entity : null;
    }
}