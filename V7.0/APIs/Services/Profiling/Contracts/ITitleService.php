<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Title;

interface ITitleService
{
    /**
     * Gets a paginated list of Title entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Title|Titles|null An associative array containing 'data' and 'total'.
     */
    public function getTitles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Title|Titles|null;

    /**
     * Creates a new Title and assigns roles.
     *
     * @param array $data
     * @return Title The newly created Title entity.
     */
    public function setTitle(array $data): Title;

    /**
     * Updates an existing Title
     *
     * @param string $id
     * @param array $data
     * @return Title|null
     */
    public function putTitle(string $id, array $data): ?Title;

    /**
     * Deletes a Title and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteTitle(string $id): bool;
}