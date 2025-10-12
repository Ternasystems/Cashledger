<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\ContactTypes;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\ContactType;

/**
 * @extends Repository<ContactType, ContactTypes>
 */
class ContactTypeRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, ContactType::class, ContactTypes::class);
    }
}