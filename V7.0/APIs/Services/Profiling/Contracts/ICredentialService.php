<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Model\Credential;

interface ICredentialService
{
    /**
     * Gets a paginated list of Credential entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Credential|Credentials|null An associative array containing 'data' and 'total'.
     */
    public function getCredentials(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Credential|Credentials|null;

    /**
     * Creates a new Credential and assigns roles.
     *
     * @param array $data
     * @return Credential The newly created Credential entity.
     */
    public function setCredential(array $data): Credential;

    /**
     * Updates an existing Credential
     *
     * @param string $id
     * @param array $data
     * @return Credential|null
     */
    public function putCredential(string $id, array $data): ?Credential;

    /**
     * Deletes a Credential and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCredential(string $id): bool;

    /**
     * Disable a Credential and its associated role relations
     *
     * @param string $id
     * @return bool
     */
    public function disableCredential(string $id): bool;

    /**
     * Reset or Update a Credential password
     *
     * @param string $id
     * @param string|null $password
     * @return bool
     */
    public function putPassword(string $id, ?string $password = null): bool;
}