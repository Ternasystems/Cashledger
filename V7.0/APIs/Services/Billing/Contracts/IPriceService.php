<?php

namespace API_Billing_Contract;

use API_Administration_Service\ReloadMode;
use API_BillingEntities_Collection\Prices;
use API_BillingEntities_Model\Price;

interface IPriceService
{
    /**
     * Gets a paginated list of Price entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Price|Prices|null An associative array containing 'data' and 'total'.
     */
    public function getPrices(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Price|Prices|null;

    /**
     * Creates a new Price and assigns roles.
     *
     * @param array $data
     * @return Price The newly created Price entity.
     */
    public function setPrice(array $data): Price;

    /**
     * Updates an existing Price
     *
     * @param string $id
     * @param array $data
     * @return Price|null
     */
    public function putPrice(string $id, array $data): ?Price;

    /**
     * Deletes a Price and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deletePrice(string $id): bool;
}