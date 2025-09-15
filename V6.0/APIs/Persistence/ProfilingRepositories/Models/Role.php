<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a user role within the system (e.g., Admin, User, Guest).
 */
class Role extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Roles';
}