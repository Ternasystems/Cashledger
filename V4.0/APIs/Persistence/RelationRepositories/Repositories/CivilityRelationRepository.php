<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\CivilityRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class CivilityRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function GetAll(): ?CivilityRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function GetById(string $id): ?CivilityRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?CivilityRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }

    public function OrderBy(Collectable $civilityRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?CivilityRelations
    {
        if (!$civilityRelations instanceof CivilityRelations)
            throw new Exception("civilityRelations must be instance of CivilityRelations");

        $collection = parent::OrderBy($civilityRelations, $properties, $orderBy);
        return $collection instanceof CivilityRelations ? $collection : null;
    }
}