<?php

namespace API_InvoicingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_InvoicingRepositories_Model\Customer;
use Closure;

/**
 * A strongly-typed collection of Customer objects.
 */
class Customers extends Collectable
{
    /**
     * Returns the first Customer in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Customer|null
     */
    public function first(?Closure $callback = null): ?Customer
    {
        $entity = parent::first($callback);
        return $entity instanceof Customer ? $entity : null;
    }

    /**
     * Returns the last Customer in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Customer|null
     */
    public function last(?Closure $callback = null): ?Customer
    {
        $entity = parent::last($callback);
        return $entity instanceof Customer ? $entity : null;
    }
}