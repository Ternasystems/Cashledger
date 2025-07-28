<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Statuses;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Status;
use Closure;

class StatusRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::first($predicate);
        return $entity instanceof Status ? $entity : null;
    }

    public function GetAll(): ?Statuses
    {
        $collection = parent::GetAll();
        return $collection instanceof Statuses ? $collection : null;
    }

    public function GetById(string $id): ?Status
    {
        $entity = parent::GetById($id);
        return $entity instanceof Status ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Statuses
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Statuses ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::last($predicate);
        return $entity instanceof Status ? $entity : null;
    }
}