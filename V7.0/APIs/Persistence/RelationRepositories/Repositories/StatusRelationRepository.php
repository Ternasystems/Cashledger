<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\StatusRelation;

/**
 * @extends Repository<StatusRelation>
 */
class StatusRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, StatusRelation::class);
    }
}