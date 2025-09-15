<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Profiles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Profile;
use Closure;

class ProfileRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Profile
    {
        $entity = parent::first($predicate);
        return $entity instanceof Profile ? $entity : null;
    }

    public function getAll(): ?Profiles
    {
        $collection = parent::getAll();
        return $collection instanceof Profiles ? $collection : null;
    }

    public function getById(string $id): ?Profile
    {
        $entity = parent::getById($id);
        return $entity instanceof Profile ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Profiles
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Profiles ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Profile
    {
        $entity = parent::last($predicate);
        return $entity instanceof Profile ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Profile)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Profile)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}