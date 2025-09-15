<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Titles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Title;
use Closure;

class TitleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Title
    {
        $entity = parent::first($predicate);
        return $entity instanceof Title ? $entity : null;
    }

    public function getAll(): ?Titles
    {
        $collection = parent::getAll();
        return $collection instanceof Titles ? $collection : null;
    }

    public function getById(string $id): ?Title
    {
        $entity = parent::getById($id);
        return $entity instanceof Title ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Titles
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Titles ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Title
    {
        $entity = parent::last($predicate);
        return $entity instanceof Title ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Title)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Title)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}