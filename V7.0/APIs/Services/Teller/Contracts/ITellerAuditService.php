<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerAudits;
use API_TellerEntities_Model\TellerAudit;

interface ITellerAuditService
{
    /**
     * Gets a paginated list of TellerAudit entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerAudit|TellerAudits|null An associative array containing 'data' and 'total'.
     */
    public function getTellerAudits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerAudit|TellerAudits|null;

    /**
     * Creates a new TellerAudit and assigns roles.
     *
     * @param array $data
     * @return TellerAudit The newly created TellerAudit entity.
     */
    public function SetTellerAudit(array $data): TellerAudit;
}