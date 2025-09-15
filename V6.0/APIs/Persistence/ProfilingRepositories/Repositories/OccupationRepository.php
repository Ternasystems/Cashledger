<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Occupations;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Occupation;
use Closure;

class OccupationRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Occupation
    {
        $entity = parent::first($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }

    public function getAll(): ?Occupations
    {
        $collection = parent::getAll();
        return $collection instanceof Occupations ? $collection : null;
    }

    public function getById(string $id): ?Occupation
    {
        $entity = parent::getById($id);
        return $entity instanceof Occupation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Occupations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Occupations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Occupation
    {
        $entity = parent::last($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Occupation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Occupation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}