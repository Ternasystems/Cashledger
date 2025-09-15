<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\OccupationRelation;
use Closure;

class OccupationRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?OccupationRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function getAll(): ?OccupationRelations
    {
        $collection = parent::getAll();
        return $collection instanceof OccupationRelations ? $collection : null;
    }

    public function getById(string $id): ?OccupationRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?OccupationRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof OccupationRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?OccupationRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof OccupationRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof OccupationRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}