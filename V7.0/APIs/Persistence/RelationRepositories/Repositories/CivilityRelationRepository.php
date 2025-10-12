<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\CivilityRelation;

/**
 * @extends Repository<CivilityRelation, CivilityRelations>
 */
class CivilityRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, CivilityRelation::class, CivilityRelations::class);
    }
}