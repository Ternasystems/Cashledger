<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\WasteRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\WasteRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class WasteRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?WasteRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof WasteRelation ? $entity : null;
    }

    public function GetAll(): ?WasteRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof WasteRelations ? $collection : null;
    }

    public function GetById(string $id): ?WasteRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof WasteRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?WasteRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof WasteRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?WasteRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof WasteRelation ? $entity : null;
    }

    public function OrderBy(Collectable $wasteRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?WasteRelations
    {
        if (!$wasteRelations instanceof WasteRelations)
            throw new Exception("WasteRelations must be instance of WasteRelations");

        $collection = parent::OrderBy($wasteRelations, $properties, $orderBy);
        return $collection instanceof WasteRelations ? $collection : null;
    }
}