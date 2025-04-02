<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Statuses;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Status;
use Exception;
use TS_Utility\Enums\OrderEnum;

class StatusRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Statuses
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Statuses ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Status
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Status ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $statuses, array $properties, array $orderBy = [OrderEnum::ASC]): ?Statuses
    {
        if (!$statuses instanceof Statuses)
            throw new Exception("Statuses must be instance of Statuses");

        $collection = parent::OrderBy($statuses, $properties, $orderBy);
        return $collection instanceof Statuses ? $collection : null;
    }
}