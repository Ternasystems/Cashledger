<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Country;
use Closure;

class Countries extends EntityCollectable
{
    /**
     * Returns the first Country in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Country|null
     */
    public function first(?Closure $callback = null): ?Country
    {
        $entity = parent::first($callback);
        return $entity instanceof Country ? $entity : null;
    }

    /**
     * Returns the last Country in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Country|null
     */
    public function last(?Closure $callback = null): ?Country
    {
        $entity = parent::last($callback);
        return $entity instanceof Country ? $entity : null;
    }
}