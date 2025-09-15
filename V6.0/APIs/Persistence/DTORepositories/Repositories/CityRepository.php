<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Cities;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\City;
use API_DTORepositories_Model\DTOBase;
use Closure;

class CityRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?City
    {
        $entity = parent::first($predicate);
        return $entity instanceof City ? $entity : null;
    }

    public function getAll(): ?Cities
    {
        $collection = parent::getAll();
        return $collection instanceof Cities ? $collection : null;
    }

    public function getById(string $id): ?City
    {
        $entity = parent::getById($id);
        return $entity instanceof City ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Cities
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Cities ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?City
    {
        $entity = parent::last($predicate);
        return $entity instanceof City ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof City)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof City)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}