<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\AppCategory;
use API_DTORepositories_Model\DTOBase;

class AppCategoryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?AppCategory
    {
        $entity = parent::first($whereClause);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?AppCategories
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function getById(string $id): ?AppCategory
    {
        $entity = parent::getById($id);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?AppCategories
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function last(?array $whereClause = null): ?AppCategory
    {
        $entity = parent::last($whereClause);
        return $entity instanceof AppCategory ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof AppCategory)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof AppCategory)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}