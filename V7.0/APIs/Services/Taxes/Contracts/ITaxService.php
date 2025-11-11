<?php

namespace API_Taxes_Contract;

use API_Administration_Service\ReloadMode;
use API_TaxesEntities_Collection\Taxes;
use API_TaxesEntities_Model\Tax;

interface ITaxService
{
    /**
     * Gets a paginated list of Tax entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Tax|Taxes|null An associative array containing 'data' and 'total'.
     */
    public function getTaxes(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Tax|Taxes|null;

    /**
     * Creates a new Tax and assigns roles.
     *
     * @param array $data
     * @return Tax The newly created Tax entity.
     */
    public function SetTax(array $data): Tax;

    /**
     * Updates an existing Tax
     *
     * @param string $id
     * @param array $data
     * @return Tax|null
     */
    public function PutTax(string $id, array $data): ?Tax;

    /**
     * Deletes a Tax and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeleteTax(string $id): bool;
}