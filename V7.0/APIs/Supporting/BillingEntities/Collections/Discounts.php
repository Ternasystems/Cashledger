<?php

namespace API_BillingEntities_Collection;

use API_BillingEntities_Model\Discount;
use API_DTOEntities_Collection\EntityCollectable;
use Closure;

class Discounts extends EntityCollectable
{
    /**
     * Returns the first Discount in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Discount|null
     */
    public function first(?Closure $callback = null): ?Discount
    {
        $entity = parent::first($callback);
        return $entity instanceof Discount ? $entity : null;
    }

    /**
     * Returns the last Discount in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Discount|null
     */
    public function last(?Closure $callback = null): ?Discount
    {
        $entity = parent::last($callback);
        return $entity instanceof Discount ? $entity : null;
    }
}