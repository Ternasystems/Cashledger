<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Model\App;

interface IAppService
{
    /**
     * Gets a paginated list of App entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return App|Apps|null An associative array containing 'data' and 'total'.
     */
    public function getApps(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): App|Apps|null;

    /**
     * Creates a new App and assigns roles.
     *
     * @param array $data
     * @return App The newly created App entity.
     */
    public function setApp(array $data): App;

    /**
     * Updates an existing App
     *
     * @param string $id
     * @param array $data
     * @return App|null
     */
    public function putApp(string $id, array $data): ?App;

    /**
     * Deletes a App and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteApp(string $id): bool;
}