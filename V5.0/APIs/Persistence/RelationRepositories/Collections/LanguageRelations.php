<?php

namespace API_RelationRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_RelationRepositories_Model\LanguageRelation;

class LanguageRelations extends Collectable
{
    public function __construct(array $collection)
    {
        parent::__construct($collection);
    }

    public function FirstOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof LanguageRelation ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?LanguageRelation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof LanguageRelation ? $entity : null;
    }
}