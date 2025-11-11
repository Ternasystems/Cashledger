<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerReceipt;
use Closure;

class TellerReceipts extends EntityCollectable
{
    /**
     * Returns the first TellerReceipt in the collection, optionally filtered by a callback.
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
     * Returns the last TellerReceipt in the collection, optionally filtered by a callback.
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