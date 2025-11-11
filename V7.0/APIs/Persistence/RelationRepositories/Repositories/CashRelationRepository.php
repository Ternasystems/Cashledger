<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\CashRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\CashRelation;

/**
 * @extends Repository<CashRelation, CashRelations>
 */
class CashRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, CashRelation::class, CashRelations::class);
    }
}