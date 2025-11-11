<?php

namespace API_BillingEntities_Collection;

use API_BillingEntities_Model\Price;
use API_DTOEntities_Collection\EntityCollectable;
use Closure;

class Prices extends EntityCollectable
{
    /**
     * Returns the first Price in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Price|null
     */
    public function first(?Closure $callback = null): ?Price
    {
        $entity = parent::first($callback);
        return $entity instanceof Price ? $entity : null;
    }

    /**
     * Returns the last Price in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Price|null
     */
    public function last(?Closure $callback = null): ?Price
    {
        $entity = parent::last($callback);
        return $entity instanceof Price ? $entity : null;
    }
}