<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
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

    public function first(?Closure $predicate = null): ?Tracking
    {
        $entity = parent::first($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function getAll(): ?Trackings
    {
        $collection = parent::getAll();
        return $collection instanceof Trackings ? $collection : null;
    }

    public function getById(string $id): ?Tracking
    {
        $entity = parent::getById($id);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Trackings
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Trackings ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Tracking
    {
        $entity = parent::last($predicate);
        return $entity instanceof Tracking ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Tracking)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Tracking)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}