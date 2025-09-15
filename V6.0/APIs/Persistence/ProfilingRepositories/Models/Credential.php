<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Contract\IProfile;

/**
 * Represents user credentials for authentication.
 */
class Credential extends DTOBase implements IProfile
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Credentials';
}