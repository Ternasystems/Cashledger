<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerSessions;
use API_TellerEntities_Model\TellerSession;

interface ITellerSessionService
{
    /**
     * Gets a paginated list of TellerSession entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerSession|TellerSessions|null An associative array containing 'data' and 'total'.
     */
    public function getTellerSessions(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerSession|TellerSessions|null;

    /**
     * Creates a new TellerSession and assigns roles.
     *
     * @param array $data
     * @return TellerSession The newly created TellerSession entity.
     */
    public function SetTellerSession(array $data): TellerSession;
}