<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;

interface IProfileService
{
    /**
     * Gets a paginated list of Profile entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Profile|Profiles|null An associative array containing 'data' and 'total'.
     */
    public function getProfiles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Profile|Profiles|null;

    /**
     * Creates a new Profile and its associated relations.
     *
     * @param array $data The data for the new contact.
     * @return Profile The newly created Profile entity.
     */
    public function setProfile(array $data): Profile;

    /**
     * Updates an existing Profile.
     *
     * @param string $id The ID of the contact to update.
     * @param array $data The new data for the contact.
     * @return void The updated Profile entity.
     */
    public function putProfile(string $id, array $data): ?Profile;

    /**
     * Deletes a Profile.
     *
     * @param string $id The ID of the contact to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteProfile(string $id): bool;

    /**
     * Disable a Profile and its associated role relations
     *
     * @param string $id
     * @return bool
     */
    public function disableProfile(string $id): bool;
}