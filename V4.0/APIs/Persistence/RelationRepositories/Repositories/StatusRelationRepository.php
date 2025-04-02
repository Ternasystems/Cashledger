<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\StatusRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class StatusRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function GetAll(): ?StatusRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function GetById(string $id): ?StatusRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?StatusRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function OrderBy(Collectable $statusRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?StatusRelations
    {
        if (!$statusRelations instanceof StatusRelations)
            throw new Exception("statusRelations must be instance of StatusRelations");

        $collection = parent::OrderBy($statusRelations, $properties, $orderBy);
        return $collection instanceof StatusRelations ? $collection : null;
    }
}