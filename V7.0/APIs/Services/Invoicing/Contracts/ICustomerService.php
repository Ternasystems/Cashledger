<?php

namespace API_Invoicing_Contract;

use API_Administration_Service\ReloadMode;
use API_InvoicingEntities_Collection\Customers;
use API_InvoicingEntities_Model\Customer;

interface ICustomerService
{
    /**
     * Gets a paginated list of Customer entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Customer|Customers|null An associative array containing 'data' and 'total'.
     */
    public function getCustomers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Customer|Customers|null;

    /**
     * Creates a new Customer and assigns roles.
     *
     * @param array $data
     * @return Customer The newly created Customer entity.
     */
    public function setCustomer(array $data): Customer;

    /**
     * Updates an existing Customer
     *
     * @param string $id
     * @param array $data
     * @return Customer|null
     */
    public function putCustomer(string $id, array $data): ?Customer;

    /**
     * Deletes a Customer and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCustomer(string $id): bool;

    /**
     * Disable a Customer and its associated role relations
     *
     * @param string $id
     * @return bool
     */
    public function disableCustomer(string $id): bool;
}