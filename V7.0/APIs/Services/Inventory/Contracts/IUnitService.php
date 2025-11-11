<?php

namespace API_Inventory_Contract;

use API_Administration_Service\ReloadMode;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;

interface IUnitService
{
    /**
     * Gets a paginated list of Unit entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Unit|Units|null An associative array containing 'data' and 'total'.
     */
    public function getUnits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Unit|Units|null;

    /**
     * Creates a new Unit and assigns roles.
     *
     * @param array $data
     * @return Unit The newly created Unit entity.
     */
    public function SetUnit(array $data): Unit;

    /**
     * Updates an existing Unit
     *
     * @param string $id
     * @param array $data
     * @return Unit|null
     */
    public function PutUnit(string $id, array $data): ?Unit;

    /**
     * Deletes a Unit and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeleteUnit(string $id): bool;
}