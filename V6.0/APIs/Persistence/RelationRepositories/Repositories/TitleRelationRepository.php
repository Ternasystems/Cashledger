<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\TitleRelation;
use Closure;

class TitleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?array $whereClause = null): ?TitleRelation
    {
        $entity = parent::first($whereClause);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function getAll(?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?TitleRelations
    {
        $collection = parent::getAll($limit, $offset, $orderBy);
        return $collection instanceof TitleRelations ? $collection : null;
    }

    public function getById(string $id): ?TitleRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?TitleRelations
    {
        $collection = parent::getBy($whereClause, $limit, $offset, $orderBy);
        return $collection instanceof TitleRelations ? $collection : null;
    }

    public function last(?array $whereClause = null): ?TitleRelation
    {
        $entity = parent::last($whereClause);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof TitleRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof TitleRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}