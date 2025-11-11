<?php

namespace API_HrmEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_HrmEntities_Model\Employee;
use Closure;

class Employees extends EntityCollectable
{
    /**
     * Returns the first Employee in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Employee|null
     */
    public function first(?Closure $callback = null): ?Employee
    {
        $entity = parent::first($callback);
        return $entity instanceof Employee ? $entity : null;
    }

    /**
     * Returns the last Employee in the collection, optionally filtered by a callback.
     *
     * @param Closure|null $callback
     * @return Employee|null
     */
    public function last(?Closure $callback = null): ?Employee
    {
        $entity = parent::last($callback);
        return $entity instanceof Employee ? $entity : null;
    }
}