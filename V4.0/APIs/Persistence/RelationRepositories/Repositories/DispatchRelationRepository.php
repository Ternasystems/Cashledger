<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\DispatchRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\DispatchRelation;
use TS_Utility\Enums\OrderEnum;

class DispatchRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DispatchRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DispatchRelation ? $entity : null;
    }

    public function GetAll(): ?DispatchRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof DispatchRelations ? $collection : null;
    }

    public function GetById(string $id): ?DispatchRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof DispatchRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?DispatchRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof DispatchRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DispatchRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DispatchRelation ? $entity : null;
    }

    public function OrderBy(Collectable $dispatchRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?DispatchRelations
    {
        if (!$dispatchRelations instanceof DispatchRelations)
            throw new Exception("DispatchRelations must be instance of DispatchRelations");

        $collection = parent::OrderBy($dispatchRelations, $properties, $orderBy);
        return $collection instanceof DispatchRelations ? $collection : null;
    }
}