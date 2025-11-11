<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Model\Status;

interface IStatusService
{
    /**
     * Gets a paginated list of Status entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Status|Statuses|null An associative array containing 'data' and 'total'.
     */
    public function getStatuses(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Status|Statuses|null;

    /**
     * Creates a new Status and assigns roles.
     *
     * @param array $data
     * @return Status The newly created Status entity.
     */
    public function setStatus(array $data): Status;

    /**
     * Updates an existing Status
     *
     * @param string $id
     * @param array $data
     * @return Status|null
     */
    public function putStatus(string $id, array $data): ?Status;

    /**
     * Deletes a Status and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteStatus(string $id): bool;
}