<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\TitleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\TitleRelation;

/**
 * @extends Repository<TitleRelation, TitleRelations>
 */
class TitleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, TitleRelation::class, TitleRelations::class);
    }
}