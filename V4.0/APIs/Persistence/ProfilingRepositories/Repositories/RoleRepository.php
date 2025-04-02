<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Roles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Role;
use Exception;
use TS_Utility\Enums\OrderEnum;

class RoleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Roles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Roles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Role
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Role ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $roles, array $properties, array $orderBy = [OrderEnum::ASC]): ?Roles
    {
        if (!$roles instanceof Roles)
            throw new Exception("Roles must be instance of Roles");

        $collection = parent::OrderBy($roles, $properties, $orderBy);
        return $collection instanceof Roles ? $collection : null;
    }
}