<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\StatusRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\StatusRelation;
use Closure;

class StatusRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function GetAll(): ?StatusRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function GetById(string $id): ?StatusRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof StatusRelation ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?StatusRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof StatusRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?StatusRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof StatusRelation ? $entity : null;
    }
}