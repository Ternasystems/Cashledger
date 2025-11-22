<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\AppRelation;

/**
 * @extends Repository<AppRelation>
 */
class AppRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, AppRelation::class);
    }
}