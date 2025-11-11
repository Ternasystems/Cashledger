<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Packaging;
use Closure;

/**
 * A strongly-typed collection of Packaging objects.
 */
class Packagings extends Collectable
{
    /**
     * Returns the first Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Packaging|null
     */
    public function first(?Closure $callback = null): ?Packaging
    {
        $entity = parent::first($callback);
        return $entity instanceof Packaging ? $entity : null;
    }

    /**
     * Returns the last Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Packaging|null
     */
    public function last(?Closure $callback = null): ?Packaging
    {
        $entity = parent::last($callback);
        return $entity instanceof Packaging ? $entity : null;
    }
}