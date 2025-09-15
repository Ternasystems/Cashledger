<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a user's occupation or profession.
 */
class Occupation extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Occupations';
}