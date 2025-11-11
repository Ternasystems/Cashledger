<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerReceipt;
use Closure;

class TellerReceipts extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerReceipt|null
     */
    public function first(?Closure $callback = null): ?TellerReceipt
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerReceipt ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerReceipt|null
     */
    public function last(?Closure $callback = null): ?TellerReceipt
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerReceipt ? $entity : null;
    }
}