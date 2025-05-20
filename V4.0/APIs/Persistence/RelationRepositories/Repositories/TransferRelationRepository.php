<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\TransferRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\TransferRelation;
use TS_Utility\Enums\OrderEnum;

class TransferRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TransferRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TransferRelation ? $entity : null;
    }

    public function GetAll(): ?TransferRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof TransferRelations ? $collection : null;
    }

    public function GetById(string $id): ?TransferRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof TransferRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?TransferRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof TransferRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TransferRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TransferRelation ? $entity : null;
    }

    public function OrderBy(Collectable $transferRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?TransferRelations
    {
        if (!$transferRelations instanceof TransferRelations)
            throw new Exception("TransferRelations must be instance of TransferRelations");

        $collection = parent::OrderBy($transferRelations, $properties, $orderBy);
        return $collection instanceof TransferRelations ? $collection : null;
    }
}