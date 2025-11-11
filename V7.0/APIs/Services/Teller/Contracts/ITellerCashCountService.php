<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerCashCounts;
use API_TellerEntities_Model\TellerCashCount;

interface ITellerCashCountService
{
    /**
     * Gets a paginated list of TellerCashCount entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerCashCount|TellerCashCounts|null An associative array containing 'data' and 'total'.
     */
    public function getTellerCashCounts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerCashCount|TellerCashCounts|null;

    /**
     * Creates a new TellerCashCount and assigns roles.
     *
     * @param array $data
     * @return TellerCashCount The newly created TellerCashCount entity.
     */
    public function SetTellerCashCount(array $data): TellerCashCount;
}