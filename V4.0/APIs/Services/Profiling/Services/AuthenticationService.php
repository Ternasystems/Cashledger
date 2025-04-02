<?php

namespace API_Profiling_Service;

use API_Profiling_Contract\IAuthenticationService;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_ProfilingEntities_Model\Credential;
use API_ProfilingRepositories\CredentialRepository;
use API_ProfilingRepositories_Model\LoginStatus;
use Exception;
use ReflectionException;
use TS_Exception\Classes\DBException;

class AuthenticationService implements IAuthenticationService
{
    protected CredentialFactory $credentialFactory;
    protected CredentialRepository $credentialRepository;

    public function __construct(CredentialFactory $_credentialFactory, CredentialRepository $_credentialRepository)
    {
        $this->credentialFactory = $_credentialFactory;
        $this->credentialRepository = $_credentialRepository;
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function Authenticate(string $username, string $password, string $ip): ?Credential
    {
        $credential =$this->credentialRepository->CheckCredential($username, $password, $ip);
        $authenticated = !is_null($credential);

        if ($authenticated) {
            $sessionId = $credential->Id;
            $sessionId =hash('sha256', $sessionId.rand(100, 999));

            $this->credentialRepository->SetConnectionStatus($credential->Id, true, $sessionId);
            $credential->ConnectionStatus = true;
            $credential->SessionId = $sessionId;

            $this->credentialRepository->SetLoginStatus($credential->Id, LoginStatus::LOGIN->name, $ip);
            $credential->LoginStatus = LoginStatus::LOGIN->name;
            $credential->Ip = $ip;

            $this->credentialFactory->Create();
            $credentials = $this->credentialFactory->Collectable();
            return $credentials->FirstOrDefault(fn($n) => $n->It()->Id == $credential->Id);
        }
        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function Deauthenticate(string $sessionId): void
    {
        $credential = $this->credentialRepository->FirstOrDefault(fn($n) => $n->SessionId == $sessionId);
        $authenticated = !is_null($credential);

        if ($authenticated) {
            $this->credentialRepository->SetConnectionStatus($credential->Id, false);
            $this->credentialRepository->SetLoginStatus($credential->Id, LoginStatus::LOGOUT->name, $credential->Ip);
        }
    }
}