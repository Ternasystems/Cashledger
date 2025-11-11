<?php

namespace API_TellerRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_TellerRepositories_Model\CashFigure;
use Closure;

class CashFigures extends Collectable
{
    /**
     * Returns the first App in the collection, optionally filtered by a callback.
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
     * Returns the last App in the collection, optionally filtered by a callback.
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