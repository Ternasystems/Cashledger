<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\GenderRelation;
use Closure;

class GenderRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?GenderRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?GenderRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function getById(string $id): ?GenderRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?GenderRelations
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?GenderRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof GenderRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof GenderRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}