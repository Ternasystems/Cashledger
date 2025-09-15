<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Statuses;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Status;
use Closure;

class StatusRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Status
    {
        $entity = parent::first($predicate);
        return $entity instanceof Status ? $entity : null;
    }

    public function getAll(): ?Statuses
    {
        $collection = parent::getAll();
        return $collection instanceof Statuses ? $collection : null;
    }

    public function getById(string $id): ?Status
    {
        $entity = parent::getById($id);
        return $entity instanceof Status ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Statuses
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Statuses ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Status
    {
        $entity = parent::last($predicate);
        return $entity instanceof Status ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Status)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Status)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}