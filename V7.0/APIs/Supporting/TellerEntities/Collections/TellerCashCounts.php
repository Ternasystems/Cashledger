<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerCashCount;
use Closure;

class TellerCashCounts extends EntityCollectable
{
    /**
     * Returns the first TellerCashCount in the collection, optionally filtered by a callback.
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
     * Returns the last TellerCashCount in the collection, optionally filtered by a callback.
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