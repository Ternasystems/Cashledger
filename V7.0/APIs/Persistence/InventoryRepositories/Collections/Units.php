<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Unit;
use Closure;

/**
 * A strongly-typed collection of Unit objects.
 */
class Units extends Collectable
{
    /**
     * Returns the first Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Unit|null
     */
    public function first(?Closure $callback = null): ?Unit
    {
        $entity = parent::first($callback);
        return $entity instanceof Unit ? $entity : null;
    }

    /**
     * Returns the last Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Unit|null
     */
    public function last(?Closure $callback = null): ?Unit
    {
        $entity = parent::last($callback);
        return $entity instanceof Unit ? $entity : null;
    }
}