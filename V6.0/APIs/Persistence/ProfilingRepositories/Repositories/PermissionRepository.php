<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Permissions;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Permission;
use Closure;

class PermissionRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Permission
    {
        $entity = parent::first($predicate);
        return $entity instanceof Permission ? $entity : null;
    }

    public function getAll(): ?Permissions
    {
        $collection = parent::getAll();
        return $collection instanceof Permissions ? $collection : null;
    }

    public function getById(string $id): ?Permission
    {
        $entity = parent::getById($id);
        return $entity instanceof Permission ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Permissions
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Permissions ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Permission
    {
        $entity = parent::last($predicate);
        return $entity instanceof Permission ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Permission)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Permission)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}