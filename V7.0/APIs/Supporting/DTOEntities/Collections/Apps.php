<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\App;
use Closure;

class Apps extends EntityCollectable
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