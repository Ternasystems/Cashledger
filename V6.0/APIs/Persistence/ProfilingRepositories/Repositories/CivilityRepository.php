<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Civilities;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Civility;
use Closure;

class CivilityRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Civility
    {
        $entity = parent::first($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    public function getAll(): ?Civilities
    {
        $collection = parent::getAll();
        return $collection instanceof Civilities ? $collection : null;
    }

    public function getById(string $id): ?Civility
    {
        $entity = parent::getById($id);
        return $entity instanceof Civility ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Civilities
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Civilities ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Civility
    {
        $entity = parent::last($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Civility)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Civility)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}