<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\AppRelation;
use TS_Utility\Enums\OrderEnum;

class AppRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function GetAll(): ?AppRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function GetById(string $id): ?AppRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?AppRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof AppRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AppRelation ? $entity : null;
    }

    public function OrderBy(Collectable $appRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?AppRelations
    {
        if (!$appRelations instanceof AppRelations)
            throw new Exception("AppRelations must be instance of AppRelations");

        $collection = parent::OrderBy($appRelations, $properties, $orderBy);
        return $collection instanceof AppRelations ? $collection : null;
    }
}