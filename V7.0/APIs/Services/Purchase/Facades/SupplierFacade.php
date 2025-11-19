<?php

namespace API_Purchase_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Purchase_Contract\ISupplierService;
use API_PurchaseEntities_Collection\Suppliers;
use API_PurchaseEntities_Model\Supplier;
use Exception;

/**
 * This is an "Adapter Facade" for the SupplierService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ISupplierService (getSuppliers, setSupplier, etc.).
 */
class SupplierFacade implements IFacade
{
    public function __construct(protected ISupplierService $supplierService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles suppliers.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Suppliers|Supplier
    {
        return $this->supplierService->getSuppliers($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Supplier
    {
        return $this->supplierService->setSupplier($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Supplier
    {
        return $this->supplierService->putSupplier($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->supplierService->deleteSupplier($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return $this->supplierService->disableSupplier($id);
    }
}