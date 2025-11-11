<?php

namespace API_InventoryRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Model\ProductCategory;
use Closure;

/**
 * A strongly-typed collection of ProductCategory objects.
 */
class ProductCategories extends Collectable
{
    /**
     * Returns the first Product in the collection, optionally filtered by a callback.
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
     * Returns the last Product in the collection, optionally filtered by a callback.
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