<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\GenderRelation;

/**
 * @extends Repository<GenderRelation>
 */
class GenderRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, GenderRelation::class);
    }
}