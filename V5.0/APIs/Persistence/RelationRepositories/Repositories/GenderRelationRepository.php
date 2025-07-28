<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\GenderRelation;
use Closure;

class GenderRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?GenderRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof GenderRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?GenderRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof GenderRelation ? $entity : null;
    }
}