<?php

namespace API_Profiling_Service;

use API_Profiling_Contract\ICredentialService;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_ProfilingEntities_Model\Credential;
use Exception;

class CredentialService implements ICredentialService
{
    protected Credentials $credentials;

    /**
     * @throws Exception
     */
    public function __construct(CredentialFactory $credentialFactory)
    {
        $credentialFactory->Create();
        $this->credentials = $credentialFactory->Collectable();
    }

    public function GetCredentials(callable $predicate = null): Credential|Credentials|null
    {
        if (is_null($predicate))
            return $this->credentials;

        $collection = $this->credentials->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}