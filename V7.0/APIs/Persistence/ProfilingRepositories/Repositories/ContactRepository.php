<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Contact;

/**
 * @extends Repository<Contact>
 */
class ContactRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Contact::class);
    }
}