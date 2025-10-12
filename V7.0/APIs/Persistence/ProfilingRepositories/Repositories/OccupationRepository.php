<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Occupations;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Occupation;

/**
 * @extends Repository<Occupation, Occupations>
 */
class OccupationRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Occupation::class, Occupations::class);
    }
}