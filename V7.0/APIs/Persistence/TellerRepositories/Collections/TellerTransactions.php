<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerTransaction;
use Closure;

class TellerTransactions extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerTransaction|null
     */
    public function first(?Closure $callback = null): ?TellerTransaction
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerTransaction ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerTransaction|null
     */
    public function last(?Closure $callback = null): ?TellerTransaction
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerTransaction ? $entity : null;
    }
}