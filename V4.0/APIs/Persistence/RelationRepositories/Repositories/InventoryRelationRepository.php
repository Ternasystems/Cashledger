<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\InventoryRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\InventoryRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class InventoryRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventoryRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventoryRelation ? $entity : null;
    }

    public function GetAll(): ?InventoryRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof InventoryRelations ? $collection : null;
    }

    public function GetById(string $id): ?InventoryRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof InventoryRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?InventoryRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof InventoryRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventoryRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventoryRelation ? $entity : null;
    }

    public function OrderBy(Collectable $inventoryRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?InventoryRelations
    {
        if (!$inventoryRelations instanceof InventoryRelations)
            throw new Exception("InventoryRelations must be instance of InventoryRelations");

        $collection = parent::OrderBy($inventoryRelations, $properties, $orderBy);
        return $collection instanceof InventoryRelations ? $collection : null;
    }
}