<?php

namespace API_PaymentsEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_PaymentsEntities_Model\PaymentMethod;
use Closure;

class PaymentMethods extends EntityCollectable
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