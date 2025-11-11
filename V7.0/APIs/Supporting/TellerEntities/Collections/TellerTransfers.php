<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerTransfer;
use Closure;

class TellerTransfers extends EntityCollectable
{
    /**
     * Returns the first TellerTransfer in the collection, optionally filtered by a callback.
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
     * Returns the last TellerTransfer in the collection, optionally filtered by a callback.
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