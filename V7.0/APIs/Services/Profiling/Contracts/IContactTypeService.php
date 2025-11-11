<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\ContactType;

interface IContactTypeService
{
    /**
     * Gets a paginated list of ContactType entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return ContactType|ContactTypes|null An associative array containing 'data' and 'total'.
     */
    public function getContactTypes(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): ContactType|ContactTypes|null;

    /**
     * Creates a new Contact and its associated relations.
     *
     * @param array $data The data for the new contact.
     * @return ContactType The newly created Contact entity.
     */
    public function setContactType(array $data): ContactType;

    /**
     * Updates an existing Contact.
     *
     * @param string $id The ID of the contact to update.
     * @param array $data The new data for the contact.
     * @return void The updated Contact entity.
     */
    public function putContactType(string $id, array $data): ?ContactType;

    /**
     * Deletes a Contact.
     *
     * @param string $id The ID of the contact to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteContactType(string $id): bool;
}