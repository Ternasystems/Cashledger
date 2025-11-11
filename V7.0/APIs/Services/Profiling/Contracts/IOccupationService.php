<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Model\Occupation;

interface IOccupationService
{
    /**
     * Gets a paginated list of Occupation entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Occupation|Occupations|null An associative array containing 'data' and 'total'.
     */
    public function getOccupations(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Occupation|Occupations|null;

    /**
     * Creates a new Occupation and assigns roles.
     *
     * @param array $data
     * @return Occupation The newly created Occupation entity.
     */
    public function setOccupation(array $data): Occupation;

    /**
     * Updates an existing Occupation
     *
     * @param string $id
     * @param array $data
     * @return Occupation|null
     */
    public function putOccupation(string $id, array $data): ?Occupation;

    /**
     * Deletes a Occupation and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteOccupation(string $id): bool;
}