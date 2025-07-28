<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\LanguageRelation;
use Closure;

class LanguageRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::first($predicate);
        return $entity instanceof LanguageRelation ? $entity : null;
    }

    public function GetAll(): ?LanguageRelations
    {
        $collection = parent::GetAll();
        return $collection instanceof LanguageRelations ? $collection : null;
    }

    public function GetById(string $id): ?LanguageRelation
    {
        $entity = parent::GetById($id);
        return $entity instanceof LanguageRelation ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?LanguageRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof LanguageRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::last($predicate);
        return $entity instanceof LanguageRelation ? $entity : null;
    }
}