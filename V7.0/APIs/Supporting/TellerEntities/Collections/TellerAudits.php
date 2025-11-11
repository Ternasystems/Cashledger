<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\TellerAudit;
use Closure;

class TellerAudits extends EntityCollectable
{
    /**
     * Returns the first TellerAudit in the collection, optionally filtered by a callback.
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
     * Returns the last TellerAudit in the collection, optionally filtered by a callback.
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