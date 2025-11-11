<?php

namespace API_Profiling_Contract;

use API_Administration_Service\ReloadMode;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Model\Gender;

interface IGenderService
{
    /**
     * Gets a paginated list of Gender entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Gender|Genders|null An associative array containing 'data' and 'total'.
     */
    public function getGenders(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Gender|Genders|null;

    /**
     * Creates a new Gender and assigns roles.
     *
     * @param array $data
     * @return Gender The newly created Gender entity.
     */
    public function setGender(array $data): Gender;

    /**
     * Updates an existing Gender
     *
     * @param string $id
     * @param array $data
     * @return Gender|null
     */
    public function putGender(string $id, array $data): ?Gender;

    /**
     * Deletes a Gender and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteGender(string $id): bool;
}