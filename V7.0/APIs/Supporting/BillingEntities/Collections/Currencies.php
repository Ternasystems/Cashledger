<?php

namespace API_BillingEntities_Collection;

use API_BillingEntities_Model\Currency;
use API_DTOEntities_Collection\EntityCollectable;
use Closure;

class Currencies extends EntityCollectable
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