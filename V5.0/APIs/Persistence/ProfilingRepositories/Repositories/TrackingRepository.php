<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Trackings;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Tracking;
use Closure;

class TrackingRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Trackings
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Trackings ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Tracking
    {
        $entity = parent::last($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }
}