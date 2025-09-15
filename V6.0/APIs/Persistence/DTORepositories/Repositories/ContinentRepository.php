<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Continents;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Continent;
use API_DTORepositories_Model\DTOBase;
use Closure;

class ContinentRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Continent
    {
        $entity = parent::first($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    public function getAll(): ?Continents
    {
        $collection = parent::getAll();
        return $collection instanceof Continents ? $collection : null;
    }

    public function getById(string $id): ?Continent
    {
        $entity = parent::getById($id);
        return $entity instanceof Continent ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Continents
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Continents ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Continent
    {
        $entity = parent::last($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Continent)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Continent)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}