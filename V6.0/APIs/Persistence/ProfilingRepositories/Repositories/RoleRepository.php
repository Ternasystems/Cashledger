<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Roles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Role;

class RoleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?Role
    {
        $entity = parent::first($whereClause);
        return $entity instanceof Role ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Roles
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Roles ? $collection : null;
    }

    public function getById(string $id): ?Role
    {
        $entity = parent::getById($id);
        return $entity instanceof Role ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Roles
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Roles ? $collection : null;
    }

    public function last(?array $whereClause = null): ?Role
    {
        $entity = parent::last($whereClause);
        return $entity instanceof Role ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Role)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Role)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}