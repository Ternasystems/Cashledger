<?php

namespace API_ProfilingRepositories;

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

    public function FirstOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Permissions
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Permissions ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Permission
    {
        $entity = parent::last($predicate);
        return $entity instanceof Permission ? $entity : null;
    }
}