<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\App;
use API_DTORepositories_Model\DTOBase;

class AppRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?App
    {
        $entity = parent::first($whereClause);
        return $entity instanceof App ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Apps
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Apps ? $collection : null;
    }

    public function getById(string $id): ?App
    {
        $entity = parent::getById($id);
        return $entity instanceof App ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Apps
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Apps ? $collection : null;
    }

    public function last(?array $whereClause = null): ?App
    {
        $entity = parent::last($whereClause);
        return $entity instanceof App ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof App)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof App)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}