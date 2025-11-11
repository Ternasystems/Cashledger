<?php

namespace API_Payments_Contract;

use API_Administration_Service\ReloadMode;
use API_PaymentsEntities_Collection\PaymentMethods;
use API_PaymentsEntities_Model\PaymentMethod;

interface IPaymentMethodService
{
    /**
     * Gets a paginated list of PaymentMethod entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return PaymentMethod|PaymentMethods|null An associative array containing 'data' and 'total'.
     */
    public function getPaymentMethods(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): PaymentMethod|PaymentMethods|null;

    /**
     * Creates a new PaymentMethod and assigns roles.
     *
     * @param array $data
     * @return PaymentMethod The newly created PaymentMethod entity.
     */
    public function SetPaymentMethod(array $data): PaymentMethod;

    /**
     * Updates an existing PaymentMethod
     *
     * @param string $id
     * @param array $data
     * @return PaymentMethod|null
     */
    public function PutPaymentMethod(string $id, array $data): ?PaymentMethod;

    /**
     * Deletes a PaymentMethod and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeletePaymentMethod(string $id): bool;
}