<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\InventRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\InventRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class InventRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventRelation ? $entity : null;
    }

    public function GetAll(): ?InventRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof InventRelations ? $collection : null;
    }

    public function GetById(string $id): ?InventRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof InventRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?InventRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof InventRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventRelation ? $entity : null;
    }

    public function OrderBy(Collectable $inventRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?InventRelations
    {
        if (!$inventRelations instanceof InventRelations)
            throw new Exception("InventRelations must be instance of InventRelations");

        $collection = parent::OrderBy($inventRelations, $properties, $orderBy);
        return $collection instanceof InventRelations ? $collection : null;
    }
}