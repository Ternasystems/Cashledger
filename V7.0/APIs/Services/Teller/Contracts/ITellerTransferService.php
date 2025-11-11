<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerTransfers;
use API_TellerEntities_Model\TellerTransfer;

interface ITellerTransferService
{
    /**
     * Gets a paginated list of TellerTransfer entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerTransfer|TellerTransfers|null An associative array containing 'data' and 'total'.
     */
    public function getTellerTransfers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerTransfer|TellerTransfers|null;

    /**
     * Creates a new TellerTransfer and assigns roles.
     *
     * @param array $data
     * @return TellerTransfer The newly created TellerTransfer entity.
     */
    public function SetTellerTransfer(array $data): TellerTransfer;
}