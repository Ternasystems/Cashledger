<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories_Model\DTOBase;
use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\StatusRelation;
use Closure;

class StatusRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?StatusRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function getAll(): ?StatusRelations
    {
        $collection = parent::getAll();
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function getById(string $id): ?StatusRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?StatusRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?StatusRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof StatusRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof StatusRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}