<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerTransfer;
use Closure;

class TellerTransfers extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerTransfer|null
     */
    public function first(?Closure $callback = null): ?TellerTransfer
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerTransfer ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerTransfer|null
     */
    public function last(?Closure $callback = null): ?TellerTransfer
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerTransfer ? $entity : null;
    }
}