<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Trackings;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Tracking;
use Exception;
use TS_Utility\Enums\OrderEnum;

class TrackingRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function GetAll(): ?Trackings
    {
        $collection = parent::GetAll();
        return $collection instanceof Trackings ? $collection : null;
    }

    public function GetById(string $id): ?Tracking
    {
        $entity = parent::GetById($id);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Trackings
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Trackings ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function OrderBy(Collectable $trackings, array $properties, array $orderBy = [OrderEnum::ASC]): ?Trackings
    {
        if (!$trackings instanceof Trackings)
            throw new Exception("Trackings must be instance of Trackings");

        $collection = parent::OrderBy($trackings, $properties, $orderBy);
        return $collection instanceof Trackings ? $collection : null;
    }
}