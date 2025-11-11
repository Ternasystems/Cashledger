<?php

namespace API_PurchaseRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_PurchaseRepositories_Model\Supplier;
use Closure;

/**
 * A strongly-typed collection of Supplier objects.
 */
class Suppliers extends Collectable
{
    /**
     * Returns the first Supplier in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Supplier|null
     */
    public function first(?Closure $callback = null): ?Supplier
    {
        $entity = parent::first($callback);
        return $entity instanceof Supplier ? $entity : null;
    }

    /**
     * Returns the last Supplier in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Supplier|null
     */
    public function last(?Closure $callback = null): ?Supplier
    {
        $entity = parent::last($callback);
        return $entity instanceof Supplier ? $entity : null;
    }
}