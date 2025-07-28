<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;

use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\AppRelation;
use Closure;

class AppRelationRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function GetAll(): ?AppRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function GetById(string $id): ?AppRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?AppRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }
}