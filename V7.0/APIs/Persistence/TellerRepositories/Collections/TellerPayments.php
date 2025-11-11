<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerPayment;
use Closure;

class TellerPayments extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerPayment|null
     */
    public function first(?Closure $callback = null): ?TellerPayment
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerPayment ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerPayment|null
     */
    public function last(?Closure $callback = null): ?TellerPayment
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerPayment ? $entity : null;
    }
}