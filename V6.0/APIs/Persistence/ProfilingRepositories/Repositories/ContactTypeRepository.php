<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories\Repository;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\ContactTypes;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\ContactType;
use Closure;

class ContactTypeRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?ContactType
    {
        $entity = parent::first($predicate);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function getAll(): ?ContactTypes
    {
        $collection = parent::getAll();
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function getById(string $id): ?ContactType
    {
        $entity = parent::getById($id);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function getBy(Closure $predicate): ?ContactTypes
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?ContactType
    {
        $entity = parent::last($predicate);
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