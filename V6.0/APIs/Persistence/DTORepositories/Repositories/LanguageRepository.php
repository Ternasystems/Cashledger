<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Languages;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories_Model\Language;

class LanguageRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?Language
    {
        $entity = parent::first($whereClause);
        return $entity instanceof Language ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Languages
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Languages ? $collection : null;
    }

    public function getById(string $id): ?Language
    {
        $entity = parent::getById($id);
        return $entity instanceof Language ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Languages
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Languages ? $collection : null;
    }

    public function last(?array $whereClause = null): ?Language
    {
        $entity = parent::last($whereClause);
        return $entity instanceof Language ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Language)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Language)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}