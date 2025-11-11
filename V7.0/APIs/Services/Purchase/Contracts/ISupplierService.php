<?php

namespace API_Purchase_Contract;

use API_Administration_Service\ReloadMode;
use API_PurchaseEntities_Collection\Suppliers;
use API_PurchaseEntities_Model\Supplier;

interface ISupplierService
{
    /**
     * Gets a paginated list of Supplier entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Supplier|Suppliers|null An associative array containing 'data' and 'total'.
     */
    public function getSuppliers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Supplier|Suppliers|null;

    /**
     * Creates a new Supplier and assigns roles.
     *
     * @param array $data
     * @return Supplier The newly created Supplier entity.
     */
    public function setSupplier(array $data): Supplier;

    /**
     * Updates an existing Supplier
     *
     * @param string $id
     * @param array $data
     * @return Supplier|null
     */
    public function putSupplier(string $id, array $data): ?Supplier;

    /**
     * Deletes a Supplier and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteSupplier(string $id): bool;

    /**
     * Disable a Supplier and its associated role relations
     *
     * @param string $id
     * @return bool
     */
    public function disableSupplier(string $id): bool;
}