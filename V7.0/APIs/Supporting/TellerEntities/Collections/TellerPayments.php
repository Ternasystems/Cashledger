<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerPayment;
use Closure;

class TellerPayments extends EntityCollectable
{
    /**
     * Returns the first TellerPayment in the collection, optionally filtered by a callback.
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
     * Returns the last TellerPayment in the collection, optionally filtered by a callback.
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