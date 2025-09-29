<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ContactRelation;
use Closure;

class ContactRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?ContactRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ContactRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function getById(string $id): ?ContactRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ContactRelations
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?ContactRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof ContactRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof ContactRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }

    public function deactivate(string $id): void
    {
        $this->context->Delete($this->modelClass, [$id, true]);
    }
}