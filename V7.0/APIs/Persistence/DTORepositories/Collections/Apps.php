<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\App;
use Closure;

/**
 * A strongly-typed collection of App objects.
 */
class Apps extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return App|null
     */
    public function first(?Closure $callback = null): ?App
    {
        $entity = parent::first($callback);
        return $entity instanceof App ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return App|null
     */
    public function last(?Closure $callback = null): ?App
    {
        $entity = parent::last($callback);
        return $entity instanceof App ? $entity : null;
    }
}