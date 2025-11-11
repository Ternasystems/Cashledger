<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\Teller;
use Closure;

class Tellers extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Teller|null
     */
    public function first(?Closure $callback = null): ?Teller
    {
        $entity = parent::first($callback);
        return $entity instanceof Teller ? $entity : null;
    }

    /**
     * Returns the last App in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Teller|null
     */
    public function last(?Closure $callback = null): ?Teller
    {
        $entity = parent::last($callback);
        return $entity instanceof Teller ? $entity : null;
    }
}