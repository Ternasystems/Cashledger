<?php

namespace API_TellerEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_TellerEntities_Model\CashFigure;
use Closure;

class CashFigures extends EntityCollectable
{
    /**
     * Returns the first CashFigure in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CashFigure|null
     */
    public function first(?Closure $callback = null): ?CashFigure
    {
        $entity = parent::first($callback);
        return $entity instanceof CashFigure ? $entity : null;
    }

    /**
     * Returns the last CashFigure in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return CashFigure|null
     */
    public function last(?Closure $callback = null): ?CashFigure
    {
        $entity = parent::last($callback);
        return $entity instanceof CashFigure ? $entity : null;
    }
}