<?php

namespace API_Inventory_Contract;

use API_Administration_Service\ReloadMode;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\Stock;

interface IStockService
{
    /**
     * Gets a paginated list of Stock entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Stock|Stocks|null An associative array containing 'data' and 'total'.
     */
    public function getStocks(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Stock|Stocks|null;

    /**
     * Creates a new Stock and assigns roles.
     *
     * @param array $data
     * @return Stock The newly created Stock entity.
     */
    public function setStock(array $data): Stock;

    /**
     * Updates an existing Stock
     *
     * @param string $id
     * @param array $data
     * @return Stock|null
     */
    public function putStock(string $id, array $data): ?Stock;

    /**
     * Updates the quantity of an existing Stock
     *
     * @param string $id
     * @param float $quantity
     * @return Stock|null
     */
    public function putQuantity(string $id, float $quantity): ?Stock;

    /**
     * Deletes a Stock and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteStock(string $id): bool;
}