<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories\Repository;
use API_DTORepositories_Model\DTOBase;
use API_RelationRepositories_Collection\ParameterRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ParameterRelation;

class ParameterRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?ParameterRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof ParameterRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ParameterRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof ParameterRelations ? $collection : null;
    }

    public function getById(string $id): ?ParameterRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof ParameterRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?ParameterRelations
    {
        $collection = parent::getBy($whereClause);
        return $collection instanceof ParameterRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?ParameterRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof ParameterRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof ParameterRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof ParameterRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}