<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Audits;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Audit;
use API_DTORepositories_Model\DTOBase;

class AuditRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?Audit
    {
        $entity = parent::first($whereClause);
        return $entity instanceof Audit ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Audits
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof Audits ? $collection : null;
    }

    public function getById(string $id): ?Audit
    {
        $entity = parent::getById($id);
        return $entity instanceof Audit ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Audits
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof Audits ? $collection : null;
    }

    public function last(?array $whereClause = null): ?Audit
    {
        $entity = parent::last($whereClause);
        return $entity instanceof Audit ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Audit)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Audit)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}