<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Cities;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\City;
use API_DTORepositories_Model\DTOBase;

class CityRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?City
    {
        $entity = parent::first($whereClause);
        return $entity instanceof City ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Cities
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Cities ? $collection : null;
    }

    public function getById(string $id): ?City
    {
        $entity = parent::getById($id);
        return $entity instanceof City ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Cities
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Cities ? $collection : null;
    }

    public function last(?array $whereClause = null): ?City
    {
        $entity = parent::last($whereClause);
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