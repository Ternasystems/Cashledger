<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\ProductCategory;
use Closure;

class ProductCategories extends EntityCollectable
{
    /**
     * Returns the first ProductCategory in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ProductCategory|null
     */
    public function first(?Closure $callback = null): ?ProductCategory
    {
        $entity = parent::first($callback);
        return $entity instanceof ProductCategory ? $entity : null;
    }

    /**
     * Returns the last ProductCategory in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return ProductCategory|null
     */
    public function last(?Closure $callback = null): ?ProductCategory
    {
        $entity = parent::last($callback);
        return $entity instanceof ProductCategory ? $entity : null;
    }
}