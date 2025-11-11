<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Model\TellerTransaction;

interface ITellerTransactionService
{
    /**
     * Gets a paginated list of TellerTransaction entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerTransaction|TellerTransactions|null An associative array containing 'data' and 'total'.
     */
    public function getTellerTransactions(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerTransaction|TellerTransactions|null;

    /**
     * Creates a new TellerTransaction and assigns roles.
     *
     * @param array $data
     * @return TellerTransaction The newly created TellerTransaction entity.
     */
    public function SetTellerTransaction(array $data): TellerTransaction;
}