<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Model\Credential;

interface ICredentialService
{
    public function GetCredentials(callable $predicate = null): Credential|Credentials|null;
}