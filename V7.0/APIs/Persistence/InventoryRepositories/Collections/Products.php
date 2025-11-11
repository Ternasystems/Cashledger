<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\Product;
use Closure;

/**
 * A strongly-typed collection of Product objects.
 */
class Products extends Collectable
{
    /**
     * Returns the first Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Product|null
     */
    public function first(?Closure $callback = null): ?Product
    {
        $entity = parent::first($callback);
        return $entity instanceof Product ? $entity : null;
    }

    /**
     * Returns the last Product in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Product|null
     */
    public function last(?Closure $callback = null): ?Product
    {
        $entity = parent::last($callback);
        return $entity instanceof Product ? $entity : null;
    }
}