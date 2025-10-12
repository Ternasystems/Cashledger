<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Civility;
use Closure;

class Civilities extends Collectable
{
    /**
     * Returns the first Civility in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Civility|null
     */
    public function first(?Closure $callback = null): ?Civility
    {
        $entity = parent::first($callback);
        return $entity instanceof Civility ? $entity : null;
    }

    /**
     * Returns the last Civility in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Civility|null
     */
    public function last(?Closure $callback = null): ?Civility
    {
        $entity = parent::last($callback);
        return $entity instanceof Civility ? $entity : null;
    }
}