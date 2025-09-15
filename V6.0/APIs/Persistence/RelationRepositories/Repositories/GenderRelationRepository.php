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

    public function first(?Closure $predicate = null): ?GenderRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function getAll(): ?GenderRelations
    {
        $collection = parent::getAll();
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function getById(string $id): ?GenderRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?GenderRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?GenderRelation
    {
        $entity = parent::last($predicate);
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