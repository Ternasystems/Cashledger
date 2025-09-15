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

    public function first(?Closure $predicate = null): ?ContactRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function getAll(): ?ContactRelations
    {
        $collection = parent::getAll();
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function getById(string $id): ?ContactRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?ContactRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?ContactRelation
    {
        $entity = parent::last($predicate);
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
}