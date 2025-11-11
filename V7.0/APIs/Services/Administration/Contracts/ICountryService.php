<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Model\Country;

interface ICountryService
{
    /**
     * Gets a paginated list of Country entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Country|Countries|null An associative array containing 'data' and 'total'.
     */
    public function getCountries(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Country|Countries|null;

    /**
     * Creates a new Country and assigns roles.
     *
     * @param array $data
     * @return Country The newly created Country entity.
     */
    public function setCountry(array $data): Country;

    /**
     * Updates an existing Country
     *
     * @param string $id
     * @param array $data
     * @return Country|null
     */
    public function putCountry(string $id, array $data): ?Country;

    /**
     * Deletes a Country and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCountry(string $id): bool;
}