<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\OccupationRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class OccupationRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function GetAll(): ?OccupationRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof OccupationRelations ? $collection : null;
    }

    public function GetById(string $id): ?OccupationRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?OccupationRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof OccupationRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }

    public function OrderBy(Collectable $occupationRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?OccupationRelations
    {
        if (!$occupationRelations instanceof OccupationRelations)
            throw new Exception("occupationRelations must be instance of OccupationRelations");

        $collection = parent::OrderBy($occupationRelations, $properties, $orderBy);
        return $collection instanceof OccupationRelations ? $collection : null;
    }
}