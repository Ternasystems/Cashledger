<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\AttributeRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\AttributeRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class AttributeRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AttributeRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AttributeRelation ? $entity : null;
    }

    public function GetAll(): ?AttributeRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof AttributeRelations ? $collection : null;
    }

    public function GetById(string $id): ?AttributeRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof AttributeRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?AttributeRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof AttributeRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AttributeRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AttributeRelation ? $entity : null;
    }

    public function OrderBy(Collectable $attributeRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?AttributeRelations
    {
        if (!$attributeRelations instanceof AttributeRelations)
            throw new Exception("AttributeRelations must be instance of AttributeRelations");

        $collection = parent::OrderBy($attributeRelations, $properties, $orderBy);
        return $collection instanceof AttributeRelations ? $collection : null;
    }
}