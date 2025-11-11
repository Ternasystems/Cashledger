<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerAudits;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerAudit;

/**
 * @extends Repository<TellerAudit, TellerAudits>
 */
class TellerAuditRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerAudit::class, TellerAudits::class);
    }
}