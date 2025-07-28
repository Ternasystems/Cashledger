<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\CivilityRelation;
use Closure;

class CivilityRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?CivilityRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof CivilityRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?CivilityRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof CivilityRelation ? $entity : null;
    }
}