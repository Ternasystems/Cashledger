<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
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

    public function first(?Closure $predicate = null): ?Role
    {
        $entity = parent::first($predicate);
        return $entity instanceof Role ? $entity : null;
    }

    public function getAll(): ?Roles
    {
        $collection = parent::getAll();
        return $collection instanceof Roles ? $collection : null;
    }

    public function getById(string $id): ?Role
    {
        $entity = parent::getById($id);
        return $entity instanceof Role ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Roles
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Roles ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Role
    {
        $entity = parent::last($predicate);
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