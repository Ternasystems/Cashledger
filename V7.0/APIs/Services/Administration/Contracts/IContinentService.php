<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Model\Continent;

interface IContinentService
{
    /**
     * Gets a paginated list of Continent entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Continent|Continents|null An associative array containing 'data' and 'total'.
     */
    public function getContinents(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Continent|Continents|null;

    /**
     * Creates a new Continent and assigns roles.
     *
     * @param array $data
     * @return Continent The newly created Continent entity.
     */
    public function setContinent(array $data): Continent;

    /**
     * Updates an existing Continent
     *
     * @param string $id
     * @param array $data
     * @return Continent|null
     */
    public function putContinent(string $id, array $data): ?Continent;

    /**
     * Deletes a Continent and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteContinent(string $id): bool;
}