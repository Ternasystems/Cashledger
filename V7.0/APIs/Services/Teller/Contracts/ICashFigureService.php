<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\CashFigures;
use API_TellerEntities_Model\CashFigure;

interface ICashFigureService
{
    /**
     * Gets a paginated list of CashFigure entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return CashFigure|CashFigures|null An associative array containing 'data' and 'total'.
     */
    public function getCashFigures(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): CashFigure|CashFigures|null;

    /**
     * Creates a new CashFigure and assigns roles.
     *
     * @param array $data
     * @return CashFigure The newly created CashFigure entity.
     */
    public function SetCashFigure(array $data): CashFigure;

    /**
     * Updates an existing CashFigure
     *
     * @param string $id
     * @param array $data
     * @return CashFigure|null
     */
    public function PutCashFigure(string $id, array $data): ?CashFigure;

    /**
     * Deletes a CashFigure and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function DeleteCashFigure(string $id): bool;
}