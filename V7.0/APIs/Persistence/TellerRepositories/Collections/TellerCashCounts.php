<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerCashCount;
use Closure;

class TellerCashCounts extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerCashCount|null
     */
    public function first(?Closure $callback = null): ?TellerCashCount
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerCashCount ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerCashCount|null
     */
    public function last(?Closure $callback = null): ?TellerCashCount
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerCashCount ? $entity : null;
    }
}