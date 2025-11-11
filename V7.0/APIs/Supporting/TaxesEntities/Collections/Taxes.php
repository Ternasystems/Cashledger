<?php

namespace API_TaxesEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TaxesEntities_Model\Tax;
use Closure;

class Taxes extends EntityCollectable
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