<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerSessions;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerSession;

/**
 * @extends Repository<TellerSession, TellerSessions>
 */
class TellerSessionRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerSession::class, TellerSessions::class);
    }
}