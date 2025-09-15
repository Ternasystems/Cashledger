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

    public function first(?Closure $predicate = null): ?CivilityRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function getAll(): ?CivilityRelations
    {
        $collection = parent::getAll();
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function getById(string $id): ?CivilityRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?CivilityRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?CivilityRelation
    {
        $entity = parent::last($predicate);
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