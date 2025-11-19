<?php

namespace API_Billing_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Billing_Contract\IPriceService;
use API_BillingEntities_Collection\Prices;
use API_BillingEntities_Model\Price;
use Exception;

/**
 * This is an "Adapter Facade" for the PriceService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IPriceService (getPrices, setPrice, etc.).
 */
class PriceFacade implements IFacade
{
    public function __construct(protected IPriceService $priceService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles prices.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): Price|Prices|null
    {
        return $this->priceService->getPrices($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Price
    {
        return $this->priceService->setPrice($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Price
    {
        return $this->priceService->putPrice($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->priceService->deletePrice($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The IPriceService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for PriceFacade");
    }
}