<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerSession;
use Closure;

class TellerSessions extends EntityCollectable
{
    /**
     * Returns the first TellerSession in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerSession|null
     */
    public function first(?Closure $callback = null): ?TellerSession
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerSession ? $entity : null;
    }

    /**
     * Returns the last TellerSession in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerSession|null
     */
    public function last(?Closure $callback = null): ?TellerSession
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerSession ? $entity : null;
    }
}