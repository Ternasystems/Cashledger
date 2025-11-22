<?php

namespace API_RelationRepositories;

use API_DTORepositories\Repository;
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories_Model\ContactRelation;

/**
 * @extends Repository<ContactRelation>
 */
class ContactRelationRepository extends Repository
{
    public function __construct(RelationContext $context)
    {
        parent::__construct($context, ContactRelation::class);
    }
}