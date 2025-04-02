<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\GenderRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class GenderRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function GetAll(): ?GenderRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function GetById(string $id): ?GenderRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?GenderRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }

    public function OrderBy(Collectable $genderRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?GenderRelations
    {
        if (!$genderRelations instanceof GenderRelations)
            throw new Exception("genderRelations must be instance of GenderRelations");

        $collection = parent::OrderBy($genderRelations, $properties, $orderBy);
        return $collection instanceof GenderRelations ? $collection : null;
    }
}