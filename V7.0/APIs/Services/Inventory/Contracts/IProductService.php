<?php

namespace API_Inventory_Contract;

use API_Administration_Service\ReloadMode;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Model\Product;

interface IProductService
{
    /**
     * Gets a paginated list of Product entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Product|Products|null An associative array containing 'data' and 'total'.
     */
    public function getProducts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Product|Products|null;

    /**
     * Creates a new Product and assigns roles.
     *
     * @param array $data
     * @return Product The newly created Product entity.
     */
    public function SetProduct(array $data): Product;

    /**
     * Updates an existing Product
     *
     * @param string $id
     * @param array $data
     * @return Product|null
     */
    public function PutProduct(string $id, array $data): ?Product;

    /**
     * Deletes a Product and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeleteProduct(string $id): bool;
}