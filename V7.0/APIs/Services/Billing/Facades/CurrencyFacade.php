<?php

namespace API_Billing_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Billing_Contract\ICurrencyService;
use API_BillingEntities_Collection\Currencies;
use API_BillingEntities_Model\Currency;
use Exception;

/**
 * This is an "Adapter Facade" for the CurrencyService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ICurrencyService (getCurrencies, setCurrency, etc.).
 */
class CurrencyFacade implements IFacade
{
    public function __construct(protected ICurrencyService $currencyService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles currencies.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): Currency|Currencies|null
    {
        return $this->currencyService->getCurrencies($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Currency
    {
        return $this->currencyService->setCurrency($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Currency
    {
        return $this->currencyService->putCurrency($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->currencyService->deleteCurrency($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The ICurrencyService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for CurrencyFacade");
    }
}