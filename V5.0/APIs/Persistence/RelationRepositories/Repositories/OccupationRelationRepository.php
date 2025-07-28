<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\OccupationRelation;
use Closure;

class OccupationRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?OccupationRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof OccupationRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?OccupationRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof OccupationRelation ? $entity : null;
    }
}