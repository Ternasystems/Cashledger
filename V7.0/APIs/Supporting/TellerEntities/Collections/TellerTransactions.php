<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerTransaction;
use Closure;

class TellerTransactions extends EntityCollectable
{
    /**
     * Returns the first TellerTransaction in the collection, optionally filtered by a callback.
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
     * Returns the last TellerTransaction in the collection, optionally filtered by a callback.
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