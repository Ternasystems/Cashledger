<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Stock;
use Closure;

class Stocks extends EntityCollectable
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