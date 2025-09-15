<?php

namespace API_RelationRepositories;

use API_Assets\DTOException;
use API_DTORepositories\Repository;
use API_DTORepositories_Model\DTOBase;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\AppRelation;
use Closure;

class ApprelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?AppRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function getAll(): ?AppRelations
    {
        $collection = parent::getAll();
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function getById(string $id): ?AppRelation
    {
        $entity = parent::getById($id);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function getBy(Closure $predicate): ?AppRelations
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?AppRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof AppRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof AppRelation)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}