<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerReversals;
use API_TellerEntities_Model\TellerReversal;

interface ITellerReversalService
{
    /**
     * Gets a paginated list of TellerReversal entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerReversal|TellerReversals|null An associative array containing 'data' and 'total'.
     */
    public function getTellerReversals(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerReversal|TellerReversals|null;

    /**
     * Creates a new TellerReversal and assigns roles.
     *
     * @param array $data
     * @return TellerReversal The newly created TellerReversal entity.
     */
    public function SetTellerReversal(array $data): TellerReversal;
}