<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ContactRelation;
use Closure;

class ContactRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?ContactRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ContactRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ContactRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof ContactRelation ? $entity : null;
    }
}