<?php

namespace API_Billing_Contract;

use API_Administration_Service\ReloadMode;
use API_BillingEntities_Collection\Currencies;
use API_BillingEntities_Model\Currency;

interface ICurrencyService
{
    /**
     * Gets a paginated list of Currency entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Currency|Currencies|null An associative array containing 'data' and 'total'.
     */
    public function getCurrencies(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Currency|Currencies|null;

    /**
     * Creates a new Currency and assigns roles.
     *
     * @param array $data
     * @return Currency The newly created Currency entity.
     */
    public function setCurrency(array $data): Currency;

    /**
     * Updates an existing Currency
     *
     * @param string $id
     * @param array $data
     * @return Currency|null
     */
    public function putCurrency(string $id, array $data): ?Currency;

    /**
     * Deletes a Currency and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteCurrency(string $id): bool;
}