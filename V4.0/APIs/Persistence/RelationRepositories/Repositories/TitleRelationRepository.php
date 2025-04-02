<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\TitleRelation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class TitleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function GetAll(): ?TitleRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof TitleRelations ? $collection : null;
    }

    public function GetById(string $id): ?TitleRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?TitleRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof TitleRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TitleRelation ? $entity : null;
    }

    public function OrderBy(Collectable $titleRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?TitleRelations
    {
        if (!$titleRelations instanceof TitleRelations)
            throw new Exception("titleRelations must be instance of TitleRelations");

        $collection = parent::OrderBy($titleRelations, $properties, $orderBy);
        return $collection instanceof TitleRelations ? $collection : null;
    }
}