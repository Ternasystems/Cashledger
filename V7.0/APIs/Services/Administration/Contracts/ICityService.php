<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Model\City;

interface ICityService
{
    /**
     * Gets a paginated list of City entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return City|Cities|null An associative array containing 'data' and 'total'.
     */
    public function getCities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): City|Cities|null;

    /**
     * Creates a new City and assigns roles.
     *
     * @param array $data
     * @return City The newly created City entity.
     */
    public function setCity(array $data): City;

    /**
     * Updates an existing City
     *
     * @param string $id
     * @param array $data
     * @return City|null
     */
    public function putCity(string $id, array $data): ?City;

    /**
     * Deletes a City and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCity(string $id): bool;
}