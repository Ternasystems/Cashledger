<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\AppCategory;
use Closure;

/**
 * A strongly-typed collection of AppCategory objects.
 */
class AppCategories extends Collectable
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