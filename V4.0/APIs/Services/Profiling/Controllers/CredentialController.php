<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\ICredentialService;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Model\Credential;
use TS_Controller\Classes\BaseController;

class CredentialController extends BaseController
{
    private ICredentialService $service;

    public function __construct(ICredentialService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Credentials
    {
        return $this->service->GetCredentials();
    }

    public function GetById(string $id): ?Credential
    {
        return $this->service->GetCredentials(fn($n) => $n->Id == $id);
    }

    public function GetByProfile(string $id): ?Credential
    {
        return $this->service->GetCredentials(fn($n) => $n->ProfileId == $id);
    }
}