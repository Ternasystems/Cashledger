<?php

namespace API_InventoryEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_InventoryEntities_Model\Unit;
use Closure;

class Units extends EntityCollectable
{
    /**
     * Returns the first Unit in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Unit|null
     */
    public function first(?Closure $callback = null): ?Unit
    {
        $entity = parent::first($callback);
        return $entity instanceof Unit ? $entity : null;
    }

    /**
     * Returns the last Unit in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Unit|null
     */
    public function last(?Closure $callback = null): ?Unit
    {
        $entity = parent::last($callback);
        return $entity instanceof Unit ? $entity : null;
    }
}