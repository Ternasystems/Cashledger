<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories\Repository;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\ContactTypes;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\ContactType;

class ContactTypeRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?ContactType
    {
        $entity = parent::first($whereClause);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ContactTypes
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function getById(string $id): ?ContactType
    {
        $entity = parent::getById($id);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ContactTypes
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function last(?array $whereClause = null): ?ContactType
    {
        $entity = parent::last($whereClause);
        return $entity instanceof ContactType ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof ContactType)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof ContactType)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}