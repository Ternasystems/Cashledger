<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Token;
use Closure;

class Tokens extends Collectable
{
    /**
     * Returns the first Token in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Token|null
     */
    public function first(?Closure $callback = null): ?Token
    {
        $entity = parent::first($callback);
        return $entity instanceof Token ? $entity : null;
    }

    /**
     * Returns the last Token in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Token|null
     */
    public function last(?Closure $callback = null): ?Token
    {
        $entity = parent::last($callback);
        return $entity instanceof Token ? $entity : null;
    }
}