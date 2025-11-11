<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerReversal;
use Closure;

class TellerReversals extends EntityCollectable
{
    /**
     * Returns the first TellerReversal in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerReversal|null
     */
    public function first(?Closure $callback = null): ?TellerReversal
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerReversal ? $entity : null;
    }

    /**
     * Returns the last TellerReversal in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerReversal|null
     */
    public function last(?Closure $callback = null): ?TellerReversal
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerReversal ? $entity : null;
    }
}