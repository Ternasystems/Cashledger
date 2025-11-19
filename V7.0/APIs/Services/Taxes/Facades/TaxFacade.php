<?php

namespace API_Taxes_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;

/**
 * This is an "Adapter Facade" for the TaxService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ITaxService (getTaxes, setTax, etc.).
 */
class TaxFacade implements IFacade
{
    public function __construct(protected ITaxService $taxService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles taxes.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Taxes|Tax
    {
        return $this->taxService->getTaxes($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Tax
    {
        return $this->taxService->setTax($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Tax
    {
        return $this->taxService->putTax($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->taxService->deleteTax($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The ITaxService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for TaxFacade");
    }
}