<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\RoleRelation;
use Closure;

class RoleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function GetAll(): ?RoleRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof RoleRelations ? $collection : null;
    }

    public function GetById(string $id): ?RoleRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?RoleRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof RoleRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof RoleRelation ? $entity : null;
    }
}