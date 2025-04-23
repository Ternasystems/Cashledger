<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\DeliveryRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\DeliveryRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class DeliveryRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DeliveryRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DeliveryRelation ? $entity : null;
    }

    public function GetAll(): ?DeliveryRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof DeliveryRelations ? $collection : null;
    }

    public function GetById(string $id): ?DeliveryRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof DeliveryRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?DeliveryRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof DeliveryRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DeliveryRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DeliveryRelation ? $entity : null;
    }

    public function OrderBy(Collectable $deliveryRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?DeliveryRelations
    {
        if (!$deliveryRelations instanceof DeliveryRelations)
            throw new Exception("DeliveryRelations must be instance of DeliveryRelations");

        $collection = parent::OrderBy($deliveryRelations, $properties, $orderBy);
        return $collection instanceof DeliveryRelations ? $collection : null;
    }
}