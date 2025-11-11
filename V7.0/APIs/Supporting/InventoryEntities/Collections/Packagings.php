<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Packaging;
use Closure;

class Packagings extends EntityCollectable
{
    /**
     * Returns the first Packaging in the collection, optionally filtered by a callback.
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
     * Returns the last Packaging in the collection, optionally filtered by a callback.
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