<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\IAuthenticationService;
use API_Profiling_Contract\ICredentialService;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Model\Credential;
use TS_Controller\Classes\BaseController;

class AuthenticationController extends BaseController
{
    private IAuthenticationService $service;
    private ICredentialService $credentialService;

    public function __construct(IAuthenticationService $_service, ICredentialService $_credentialService)
    {
        $this->service = $_service;
        $this->credentialService = $_credentialService;
    }

    public function Login(object $model): ?Credential
    {
        return $this->service->Authenticate($model->username, $model->pwd, $model->ip);
    }

    public function Logout(string $sessionId): void
    {
        $this->service->Deauthenticate($sessionId);
    }

    public function GetCredentials(): ?Credentials
    {
        return $this->credentialService->GetCredentials();
    }
}