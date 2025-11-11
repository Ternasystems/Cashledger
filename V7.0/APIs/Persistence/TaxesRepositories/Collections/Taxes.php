<?php

namespace API_TaxesRepositories_Collection;

use API_DTORepositories_Collection\Collectable;

use API_TaxesRepositories_Model\Tax;
use Closure;

/**
 * A strongly-typed collection of Tax objects.
 */
class Taxes extends Collectable
{
    /**
     * Returns the first Tax in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Tax|null
     */
    public function first(?Closure $callback = null): ?Tax
    {
        $entity = parent::first($callback);
        return $entity instanceof Tax ? $entity : null;
    }

    /**
     * Returns the last Tax in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Tax|null
     */
    public function last(?Closure $callback = null): ?Tax
    {
        $entity = parent::last($callback);
        return $entity instanceof Tax ? $entity : null;
    }
}