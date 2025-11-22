<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerAudit;

/**
 * @extends Repository<TellerAudit>
 */
class TellerAuditRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerAudit::class);
    }
}