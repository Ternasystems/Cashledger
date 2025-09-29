<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\CivilityRelation;
use Closure;

class CivilityRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?CivilityRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?CivilityRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function getById(string $id): ?CivilityRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?CivilityRelations
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?CivilityRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof CivilityRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof CivilityRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}