<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ContactRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ContactRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function GetAll(): ?ContactRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function GetById(string $id): ?ContactRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ContactRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }

    public function OrderBy(Collectable $contactRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?ContactRelations
    {
        if (!$contactRelations instanceof ContactRelations)
            throw new Exception("contactRelations must be instance of ContactRelations");

        $collection = parent::OrderBy($contactRelations, $properties, $orderBy);
        return $collection instanceof ContactRelations ? $collection : null;
    }
}