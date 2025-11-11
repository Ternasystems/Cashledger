<?php

namespace API_BillingRepositories_Collection;

use API_BillingRepositories_model\Price;
use API_DTORepositories_Collection\Collectable;
use Closure;

/**
 * A strongly-typed collection of Price objects.
 */
class Prices extends Collectable
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