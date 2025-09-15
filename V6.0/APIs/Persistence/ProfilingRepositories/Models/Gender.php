<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a gender identity.
 */
class Gender extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Genders';
}