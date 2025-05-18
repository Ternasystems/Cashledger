<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\ReturnRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ReturnRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ReturnRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ReturnRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ReturnRelation ? $entity : null;
    }

    public function GetAll(): ?ReturnRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof ReturnRelations ? $collection : null;
    }

    public function GetById(string $id): ?ReturnRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof ReturnRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ReturnRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ReturnRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ReturnRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ReturnRelation ? $entity : null;
    }

    public function OrderBy(Collectable $returnRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?ReturnRelations
    {
        if (!$returnRelations instanceof ReturnRelations)
            throw new Exception("ReturnRelations must be instance of ReturnRelations");

        $collection = parent::OrderBy($returnRelations, $properties, $orderBy);
        return $collection instanceof ReturnRelations ? $collection : null;
    }
}