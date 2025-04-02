<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use Exception;
use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\LanguageRelation;
use TS_Utility\Enums\OrderEnum;

class LanguageRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?LanguageRelations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof LanguageRelations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof LanguageRelation ? $entity : null;
    }

    public function OrderBy(Collectable $languageRelations, array $properties, array $orderBy = [OrderEnum::ASC]): ?LanguageRelations
    {
        if (!$languageRelations instanceof LanguageRelations)
            throw new Exception("LanguageRelations must be instance of LanguageRelations");

        $collection = parent::OrderBy($languageRelations, $properties, $orderBy);
        return $collection instanceof LanguageRelations ? $collection : null;
    }
}