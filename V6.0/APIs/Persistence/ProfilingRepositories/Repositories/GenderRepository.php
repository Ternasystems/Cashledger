<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Genders;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Gender;
use Closure;

class GenderRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Gender
    {
        $entity = parent::first($predicate);
        return $entity instanceof Gender ? $entity : null;
    }

    public function getAll(): ?Genders
    {
        $collection = parent::getAll();
        return $collection instanceof Genders ? $collection : null;
    }

    public function getById(string $id): ?Gender
    {
        $entity = parent::getById($id);
        return $entity instanceof Gender ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Genders
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Genders ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Gender
    {
        $entity = parent::last($predicate);
        return $entity instanceof Gender ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Gender)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Gender)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}