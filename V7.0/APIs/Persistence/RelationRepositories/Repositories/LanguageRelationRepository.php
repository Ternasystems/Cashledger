<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\LanguageRelation;

/**
 * @extends Repository<LanguageRelation, LanguageRelations>
 */
class LanguageRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, LanguageRelation::class, LanguageRelations::class);
    }
}