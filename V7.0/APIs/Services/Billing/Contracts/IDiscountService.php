<?php

namespace API_Billing_Contract;

use API_Administration_Service\ReloadMode;
use API_BillingEntities_Collection\Discounts;
use API_BillingEntities_Model\Discount;

interface IDiscountService
{
    /**
     * Gets a paginated list of Discount entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Discount|Discounts|null An associative array containing 'data' and 'total'.
     */
    public function getDiscounts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Discount|Discounts|null;

    /**
     * Creates a new Discount and assigns roles.
     *
     * @param array $data
     * @return Discount The newly created Discount entity.
     */
    public function setDiscount(array $data): Discount;

    /**
     * Updates an existing Discount
     *
     * @param string $id
     * @param array $data
     * @return Discount|null
     */
    public function putDiscount(string $id, array $data): ?Discount;

    /**
     * Deletes a Discount and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteDiscount(string $id): bool;
}