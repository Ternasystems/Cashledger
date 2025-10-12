<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\OccupationRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\OccupationRelation;

/**
 * @extends Repository<OccupationRelation, OccupationRelations>
 */
class OccupationRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, OccupationRelation::class, OccupationRelations::class);
    }
}