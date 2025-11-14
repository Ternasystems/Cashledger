<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Audits;
use API_DTOEntities_Model\Audit;

interface IAuditService
{
    /**
     * Gets a paginated list of Audit entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Audit|Audits|null An associative array containing 'data' and 'total'.
     */
    public function getAudits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Audit|Audits|null;
}