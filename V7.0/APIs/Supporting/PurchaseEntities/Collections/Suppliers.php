<?php

namespace API_PurchaseEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_PurchaseEntities_Model\Supplier;
use Closure;

class Suppliers extends EntityCollectable
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