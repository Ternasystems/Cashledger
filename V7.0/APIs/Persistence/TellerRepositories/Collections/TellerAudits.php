<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\TellerAudit;
use Closure;

class TellerAudits extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerAudit|null
     */
    public function first(?Closure $callback = null): ?TellerAudit
    {
        $entity = parent::first($callback);
        return $entity instanceof TellerAudit ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return TellerAudit|null
     */
    public function last(?Closure $callback = null): ?TellerAudit
    {
        $entity = parent::last($callback);
        return $entity instanceof TellerAudit ? $entity : null;
    }
}