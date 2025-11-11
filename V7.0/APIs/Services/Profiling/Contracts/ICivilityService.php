<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Model\Civility;

interface ICivilityService
{
    /**
     * Gets a paginated list of Civility entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Civility|Civilities|null An associative array containing 'data' and 'total'.
     */
    public function getCivilities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Civility|Civilities|null;

    /**
     * Creates a new Civility and assigns roles.
     *
     * @param array $data
     * @return Civility The newly created Civility entity.
     */
    public function setCivility(array $data): Civility;

    /**
     * Updates an existing Civility
     *
     * @param string $id
     * @param array $data
     * @return Civility|null
     */
    public function putCivility(string $id, array $data): ?Civility;

    /**
     * Deletes a Civility and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCivility(string $id): bool;
}