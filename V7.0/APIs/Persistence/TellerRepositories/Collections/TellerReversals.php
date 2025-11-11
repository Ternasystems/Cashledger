<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerReversal;
use Closure;

class TellerReversals extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
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
     * Returns the last App in the collection, optionally filtered by a callback.
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