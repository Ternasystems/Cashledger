<?php

namespace API_Invoicing_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Invoicing_Contract\ICustomerService;
use API_InvoicingEntities_Collection\Customers;
use API_InvoicingEntities_Model\Customer;
use Exception;

/**
 * This is an "Adapter Facade" for the CustomerService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ICustomerService (getCustomers, setCustomer, etc.).
 */
class CustomerFacade implements IFacade
{
    public function __construct(protected ICustomerService $customerService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles customers.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Customers|Customer
    {
        return $this->customerService->getCustomers($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Customer
    {
        return $this->customerService->setCustomer($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Customer
    {
        return $this->customerService->putCustomer($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->customerService->deleteCustomer($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return $this->customerService->disableCustomer($id);
    }
}