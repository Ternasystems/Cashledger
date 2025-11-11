<?php

namespace API_HrmRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_HrmRepositories_Model\Employee;
use Closure;

/**
 * A strongly-typed collection of Employee objects.
 */
class Employees extends Collectable
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