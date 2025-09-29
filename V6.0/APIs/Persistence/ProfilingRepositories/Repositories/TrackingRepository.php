<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Trackings;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Tracking;

class TrackingRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?Tracking
    {
        $entity = parent::first($whereClause);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Trackings
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Trackings ? $collection : null;
    }

    public function getById(string $id): ?Tracking
    {
        $entity = parent::getById($id);
        return $entity instanceof Tracking ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Trackings
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Trackings ? $collection : null;
    }

    public function last(?array $whereClause = null): ?Tracking
    {
        $entity = parent::last($whereClause);
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