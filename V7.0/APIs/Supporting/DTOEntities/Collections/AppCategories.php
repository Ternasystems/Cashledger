<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\AppCategory;
use Closure;

class AppCategories extends EntityCollectable
{
    /**
     * Returns the first AppCategory in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return AppCategory|null
     */
    public function first(?Closure $callback = null): ?AppCategory
    {
        $entity = parent::first($callback);
        return $entity instanceof AppCategory ? $entity : null;
    }

    /**
     * Returns the last AppCategory in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return AppCategory|null
     */
    public function last(?Closure $callback = null): ?AppCategory
    {
        $entity = parent::last($callback);
        return $entity instanceof AppCategory ? $entity : null;
    }
}