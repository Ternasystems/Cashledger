<?php

namespace API_Payments_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Payments_Contract\IPaymentMethodService;
use API_PaymentsEntities_Collection\PaymentMethods;
use API_PaymentsEntities_Model\PaymentMethod;
use Exception;

/**
 * This is an "Adapter Facade" for the PaymentService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IPaymentMethodService (getPaymentMethods, setPaymentMethod, etc.).
 */
class PaymentFacade implements IFacade
{
    public function __construct(protected IPaymentMethodService $paymentService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles payments.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|PaymentMethods|PaymentMethod
    {
        return $this->paymentService->getPaymentMethods($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): PaymentMethod
    {
        return $this->paymentService->setPaymentMethod($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?PaymentMethod
    {
        return $this->paymentService->putPaymentMethod($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->paymentService->deletePaymentMethod($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The IPaymentMethodService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for PaymentFacade");
    }
}