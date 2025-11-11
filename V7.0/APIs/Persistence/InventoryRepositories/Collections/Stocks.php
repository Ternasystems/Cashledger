<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Stock;
use Closure;

/**
 * A strongly-typed collection of Stock objects.
 */
class Stocks extends Collectable
{
    /**
     * Returns the first Stock in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Stock|null
     */
    public function first(?Closure $callback = null): ?Stock
    {
        $entity = parent::first($callback);
        return $entity instanceof Stock ? $entity : null;
    }

    /**
     * Returns the last Stock in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Stock|null
     */
    public function last(?Closure $callback = null): ?Stock
    {
        $entity = parent::last($callback);
        return $entity instanceof Stock ? $entity : null;
    }
}