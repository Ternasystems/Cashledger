<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Roles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Role;
use Closure;

class RoleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::first($predicate);
        return $entity instanceof Role ? $entity : null;
    }

    public function GetAll(): ?Roles
    {
        $collection = parent::GetAll();
        return $collection instanceof Roles ? $collection : null;
    }

    public function GetById(string $id): ?Role
    {
        $entity = parent::GetById($id);
        return $entity instanceof Role ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Roles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Roles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::last($predicate);
        return $entity instanceof Role ? $entity : null;
    }
}