<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Countries;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Country;
use API_DTORepositories_Model\DTOBase;

class CountryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?Country
    {
        $entity = parent::first($whereClause);
        return $entity instanceof Country ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Countries
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Countries ? $collection : null;
    }

    public function getById(string $id): ?Country
    {
        $entity = parent::getById($id);
        return $entity instanceof Country ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Countries
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Countries ? $collection : null;
    }

    public function last(?array $whereClause = null): ?Country
    {
        $entity = parent::last($whereClause);
        return $entity instanceof Country ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Country)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Country)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}