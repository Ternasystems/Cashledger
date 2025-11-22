<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Role;

/**
 * @extends Repository<Role>
 */
class RoleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Role::class);
    }
}