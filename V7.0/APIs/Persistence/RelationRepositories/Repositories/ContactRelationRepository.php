<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ContactRelation;

/**
 * @extends Repository<ContactRelation, ContactRelations>
 */
class ContactRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, ContactRelation::class, ContactRelations::class);
    }
}