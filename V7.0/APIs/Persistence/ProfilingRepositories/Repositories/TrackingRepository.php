<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Tracking;

/**
 * @extends Repository<Tracking>
 */
class TrackingRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Tracking::class);
    }
}