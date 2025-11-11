<?php

namespace API_Inventory_Contract;

use API_Administration_Service\ReloadMode;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;

interface IPackagingService
{
    /**
     * Gets a paginated list of Packaging entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Packaging|Packagings|null An associative array containing 'data' and 'total'.
     */
    public function getPackagings(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Packaging|Packagings|null;

    /**
     * Creates a new Packaging and assigns roles.
     *
     * @param array $data
     * @return Packaging The newly created Packaging entity.
     */
    public function SetPackaging(array $data): Packaging;

    /**
     * Updates an existing Packaging
     *
     * @param string $id
     * @param array $data
     * @return Packaging|null
     */
    public function PutPackaging(string $id, array $data): ?Packaging;

    /**
     * Deletes a Packaging and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeletePackaging(string $id): bool;
}