<?php

namespace API_PaymentsRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_PaymentsRepositories_Model\PaymentMethod;
use Closure;

/**
 * A strongly-typed collection of PaymentMethod objects.
 */
class PaymentMethods extends Collectable
{
    /**
     * Returns the first PaymentMethod in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return PaymentMethod|null
     */
    public function first(?Closure $callback = null): ?PaymentMethod
    {
        $entity = parent::first($callback);
        return $entity instanceof PaymentMethod ? $entity : null;
    }

    /**
     * Returns the last PaymentMethod in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return PaymentMethod|null
     */
    public function last(?Closure $callback = null): ?PaymentMethod
    {
        $entity = parent::last($callback);
        return $entity instanceof PaymentMethod ? $entity : null;
    }
}