<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Civility;
use Closure;

class Civilities extends EntityCollectable
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