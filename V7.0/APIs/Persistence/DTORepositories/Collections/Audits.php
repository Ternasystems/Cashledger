<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\Audit;
use Closure;

/**
 * A strongly-typed collection of Audit objects.
 */
class Audits extends Collectable
{
    /**
     * Returns the first Audit in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Audit|null
     */
    public function first(?Closure $callback = null): ?Audit
    {
        $entity = parent::first($callback);
        return $entity instanceof Audit ? $entity : null;
    }

    /**
     * Returns the last Audit in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Audit|null
     */
    public function last(?Closure $callback = null): ?Audit
    {
        $entity = parent::last($callback);
        return $entity instanceof Audit ? $entity : null;
    }
}