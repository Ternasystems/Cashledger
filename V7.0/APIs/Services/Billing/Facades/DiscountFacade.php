<?php

namespace API_Billing_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Billing_Contract\IDiscountService;
use API_BillingEntities_Collection\Discounts;
use API_BillingEntities_Model\Discount;
use Exception;

/**
 * This is an "Adapter Facade" for the DiscountService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IDiscountService (getDiscounts, setDiscount, etc.).
 */
class DiscountFacade implements IFacade
{
    public function __construct(protected IDiscountService $discountService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles discounts.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Discounts|Discount
    {
        return $this->discountService->getDiscounts($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Discount
    {
        return $this->discountService->setDiscount($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Discount
    {
        return $this->discountService->putDiscount($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->discountService->deleteDiscount($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The IDiscountService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for DiscountFacade");
    }
}