<?php

namespace API_InvoicingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InvoicingEntities_Model\Customer;
use Closure;

class Customers extends EntityCollectable
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