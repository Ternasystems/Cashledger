<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\Language;
use Closure;

class Languages extends EntityCollectable
{
    /**
     * Returns the first Language in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Language|null
     */
    public function first(?Closure $callback = null): ?Language
    {
        $entity = parent::first($callback);
        return $entity instanceof Language ? $entity : null;
    }

    /**
     * Returns the last Language in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Language|null
     */
    public function last(?Closure $callback = null): ?Language
    {
        $entity = parent::last($callback);
        return $entity instanceof Language ? $entity : null;
    }
}