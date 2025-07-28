<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\TitleRelation;
use Closure;

class TitleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?TitleRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof TitleRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TitleRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof TitleRelation ? $entity : null;
    }
}