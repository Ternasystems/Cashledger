<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\StockRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\StockRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class StockRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StockRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof StockRelation ? $entity : null;
    }

    public function GetAll(): ?StockRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof StockRelations ? $collection : null;
    }

    public function GetById(string $id): ?StockRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof StockRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?StockRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof StockRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StockRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof StockRelation ? $entity : null;
    }

    public function OrderBy(Collectable $stockRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?StockRelations
    {
        if (!$stockRelations instanceof StockRelations)
            throw new Exception("StockRelations must be instance of StockRelations");

        $collection = parent::OrderBy($stockRelations, $properties, $orderBy);
        return $collection instanceof StockRelations ? $collection : null;
    }
}