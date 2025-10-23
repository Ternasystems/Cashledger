<?php

namespace API_Profiling_Service;

use API_Assets\Classes\EntityException;
use API_Profiling_Contract\IAuthenticationService;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_ProfilingEntities_Model\Credential;
use API_ProfilingEntities_Model\Permission;
use API_ProfilingRepositories_Model\LoginStatus;
use TS_Exception\Classes\DomainException;

class AuthenticationService implements IAuthenticationService
{
    protected CredentialFactory $credentialFactory;

    public function __construct(CredentialFactory $credentialFactory)
    {
        $this->credentialFactory = $credentialFactory;
    }

    /**
     * @throws DomainException
     */
    public function Authenticate(string $username, string $password, string $ip): ?Credential
    {
        $this->credentialFactory->filter([['UserName', '=', $username]]);
        $this->credentialFactory->Create();

        // 1. Ask the repository to validate the credentials against the database.
        $credential = $this->credentialFactory->repository()->checkCredential($username, $password, $ip);

        if (!$credential)
            return null;

        // 2. If valid, generate a secure, unique session ID.
        $sessionId = hash('sha256', $credential->Id.uniqid('', true));

        // 3. Update the user's status and session in the database.
        $this->credentialFactory->repository()->setConnectionStatus($credential->Id, true, $sessionId);
        $this->credentialFactory->repository()->setLoginStatus($credential->Id, LoginStatus::LOGIN->name, $ip);

        // 4. Use the factory to build the full, rich Credential entity to return to the caller.
        // This ensures all related data (profile, roles, etc.) is included.
        $this->credentialFactory->filter([['ID', '=', $credential->Id]]);
        $this->credentialFactory->Create();

        return $this->credentialFactory->collectable()?->first();
    }

    /**
     * @throws DomainException
     */
    public function Deauthenticate(string $sessionId): bool
    {
        $this->credentialFactory->filter([['SessionID', '=', $sessionId]]);
        $this->credentialFactory->Create();

        // Find the credential associated with the current session ID.
        $credential = $this->credentialFactory->collectable()?->first();

        if (!$credential)
            return false;

        $this->credentialFactory->repository()->setConnectionStatus($credential->Id, false);
        $this->credentialFactory->repository()->setLoginStatus($credential->Id, LoginStatus::LOGOUT->name, $credential->it()->Ip);
        return true;
    }


    /**
     * @throws DomainException
     * @throws EntityException
     */
    public function SessionPayload(string $sessionId): ?array
    {
        $this->credentialFactory->filter([['SessionID', '=', $sessionId]]);
        $this->credentialFactory->Create();

        $credential = $this->credentialFactory->collectable()?->first();

        if (!$credential)
            return null;

        $permissions = [];
        if ($roles = $credential->roles()){
            $entities = $roles->selectMany(fn($n) => $n->permissions())->toArray();
            $permissions = array_map(fn(Permission $permission) => $permission->it()->Name, $entities);
        }

        return ['credential' => $credential, 'permissions' => array_unique($permissions)];
    }
}