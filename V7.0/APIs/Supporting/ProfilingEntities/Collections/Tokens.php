<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Token;
use Closure;

class Tokens extends EntityCollectable
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