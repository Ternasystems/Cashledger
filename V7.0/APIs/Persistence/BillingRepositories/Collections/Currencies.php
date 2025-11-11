<?php

namespace API_BillingRepositories_Collection;

use API_BillingRepositories_Model\Currency;
use API_DTORepositories_Collection\Collectable;
use Closure;

/**
 * A strongly-typed collection of Currency objects.
 */
class Currencies extends Collectable
{
    /**
     * Returns the first Currency in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Currency|null
     */
    public function first(?Closure $callback = null): ?Currency
    {
        $entity = parent::first($callback);
        return $entity instanceof Currency ? $entity : null;
    }

    /**
     * Returns the last Currency in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Currency|null
     */
    public function last(?Closure $callback = null): ?Currency
    {
        $entity = parent::last($callback);
        return $entity instanceof Currency ? $entity : null;
    }
}