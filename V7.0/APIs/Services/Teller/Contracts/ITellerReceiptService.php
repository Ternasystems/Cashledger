<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerReceipts;
use API_TellerEntities_Model\TellerReceipt;

interface ITellerReceiptService
{
    /**
     * Gets a paginated list of TellerReceipt entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerReceipt|TellerReceipts|null An associative array containing 'data' and 'total'.
     */
    public function getTellerReceipts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerReceipt|TellerReceipts|null;

    /**
     * Creates a new TellerReceipt and assigns roles.
     *
     * @param array $data
     * @return TellerReceipt The newly created TellerReceipt entity.
     */
    public function SetTellerReceipt(array $data): TellerReceipt;
}