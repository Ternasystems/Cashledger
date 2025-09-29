<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Gender;
use Closure;

class Genders extends EntityCollectable
{
    /**
     * Returns the first Gender in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Gender|null
     */
    public function first(?Closure $callback = null): ?Gender
    {
        $entity = parent::first($callback);
        return $entity instanceof Gender ? $entity : null;
    }

    /**
     * Returns the last Gender in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Gender|null
     */
    public function last(?Closure $callback = null): ?Gender
    {
        $entity = parent::last($callback);
        return $entity instanceof Gender ? $entity : null;
    }
}