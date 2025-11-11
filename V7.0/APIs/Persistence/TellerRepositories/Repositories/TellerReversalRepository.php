<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerReversals;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerReversal;

/**
 * @extends Repository<TellerReversal, TellerReversals>
 */
class TellerReversalRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerReversal::class, TellerReversals::class);
    }
}