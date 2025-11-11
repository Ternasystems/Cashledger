<?php

namespace API_BillingRepositories_Collection;

use API_BillingRepositories_Model\Discount;
use API_DTORepositories_Collection\Collectable;
use Closure;

/**
 * A strongly-typed collection of Discount objects.
 */
class Discounts extends Collectable
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