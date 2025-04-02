<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\RoleRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class RoleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?RoleRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof RoleRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?RoleRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof RoleRelation ? $entity : null;
    }

    public function OrderBy(Collectable $roleRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?RoleRelations
    {
        if (!$roleRelations instanceof RoleRelations)
            throw new Exception("roleRelations must be instance of RoleRelations");

        $collection = parent::OrderBy($roleRelations, $properties, $orderBy);
        return $collection instanceof RoleRelations ? $collection : null;
    }
}