<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Permission;

/**
 * @extends Repository<Permission>
 */
class PermissionRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context, Permission::class);
    }
}