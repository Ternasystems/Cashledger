<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\PriceRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\PriceRelation;

/**
 * @extends Repository<PriceRelation, PriceRelations>
 */
class PriceRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, PriceRelation::class, PriceRelations::class);
    }
}