<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Model\Teller;

interface ITellerService
{
    /**
     * Gets a paginated list of Teller entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Teller|Tellers|null An associative array containing 'data' and 'total'.
     */
    public function getTellers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Teller|Tellers|null;

    /**
     * Creates a new Teller and assigns roles.
     *
     * @param array $data
     * @return Teller The newly created Teller entity.
     */
    public function setTeller(array $data): Teller;

    /**
     * Updates an existing Teller
     *
     * @param string $id
     * @param array $data
     * @return Teller|null
     */
    public function putTeller(string $id, array $data): ?Teller;

    /**
     * Deletes a Teller and its associated relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteTeller(string $id): bool;

    /**
     * Disable a Teller and its associated relations
     *
     * @param string $id
     * @return bool
     */
    public function disableTeller(string $id): bool;
}