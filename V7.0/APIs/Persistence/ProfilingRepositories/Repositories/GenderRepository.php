<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Gender;

/**
 * @extends Repository<Gender>
 */
class GenderRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Gender::class);
    }
}