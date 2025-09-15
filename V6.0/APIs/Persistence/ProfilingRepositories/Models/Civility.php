<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a civility title (e.g., Mr., Mrs., Ms.).
 */
class Civility extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Civilities';
}