<?php

namespace API_Profiling_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Profiling_Contract\ICredentialService;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Model\Credential;

/**
 * This is an "Adapter Facade" for the CredentialService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ICredentialService (getCredentials, setCredential, etc.).
 */
class CredentialFacade implements IFacade
{
    public function __construct(protected ICredentialService $credentialService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles credentials.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Credentials|Credential
    {
        return $this->credentialService->getCredentials($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Credential
    {
        return $this->credentialService->setCredential($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Credential
    {
        return $this->credentialService->putCredential($id, $data);
    }

    public function putPassword(string $resourceType, string $id, string $password): bool
    {
        return $this->credentialService->putPassword($id, $password);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->credentialService->deleteCredential($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The ICredentialService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for CredentialFacade");
    }
}