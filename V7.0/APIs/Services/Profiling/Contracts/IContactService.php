<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Model\Contact;

interface IContactService
{
    /**
     * Gets a paginated list of Contact entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Contact|Contacts|null An associative array containing 'data' and 'total'.
     */
    public function getContacts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Contact|Contacts|null;

    /**
     * Creates a new Contact and its associated relations.
     *
     * @param array $data The data for the new contact.
     * @return Contact The newly created Contact entity.
     */
    public function setContact(array $data): Contact;

    /**
     * Updates an existing Contact.
     *
     * @param string $id The ID of the contact to update.
     * @param array $data The new data for the contact.
     * @return void The updated Contact entity.
     */
    public function putContact(string $id, array $data): ?Contact;

    /**
     * Deletes a Contact.
     *
     * @param string $id The ID of the contact to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteContact(string $id): bool;

}