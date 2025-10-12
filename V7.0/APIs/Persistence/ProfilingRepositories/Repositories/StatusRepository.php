<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Statuses;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Status;

/**
 * @extends Repository<Status, Statuses>
 */
class StatusRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Status::class, Statuses::class);
    }
}