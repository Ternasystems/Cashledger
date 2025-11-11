<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Product;
use Closure;

class Products extends EntityCollectable
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