<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\Teller;
use Closure;

class Tellers extends EntityCollectable
{
    /**
     * Returns the first Teller in the collection, optionally filtered by a callback.
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
     * Returns the last Teller in the collection, optionally filtered by a callback.
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