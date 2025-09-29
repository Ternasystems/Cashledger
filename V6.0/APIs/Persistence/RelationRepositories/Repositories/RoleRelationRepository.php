<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\RoleRelation;
use Closure;

class RoleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?RoleRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?RoleRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof RoleRelations ? $collection : null;
    }

    public function getById(string $id): ?RoleRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?RoleRelations
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof RoleRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?RoleRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof RoleRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof RoleRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}