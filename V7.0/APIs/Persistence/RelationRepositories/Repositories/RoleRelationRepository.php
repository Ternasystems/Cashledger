<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\RoleRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\RoleRelation;

/**
 * @extends Repository<RoleRelation, RoleRelations>
 */
class RoleRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, RoleRelation::class, RoleRelations::class);
    }
}