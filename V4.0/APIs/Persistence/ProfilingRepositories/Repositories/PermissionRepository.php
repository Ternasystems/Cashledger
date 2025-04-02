<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Permissions;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Permission;
use Exception;
use TS_Utility\Enums\OrderEnum;

class PermissionRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Permission ? $entity : null;
    }

    public function GetAll(): ?Permissions
    {
        $collection = parent::GetAll();
        return $collection instanceof Permissions ? $collection : null;
    }

    public function GetById(string $id): ?Permission
    {
        $entity = parent::GetById($id);
        return $entity instanceof Permission ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Permissions
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Permissions ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Permission ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $permissions, array $properties, array $orderBy = [OrderEnum::ASC]): ?Permissions
    {
        if (!$permissions instanceof Permissions)
            throw new Exception("Permissions must be instance of Permissions");

        $collection = parent::OrderBy($permissions, $properties, $orderBy);
        return $collection instanceof Permissions ? $collection : null;
    }
}