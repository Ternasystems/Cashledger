<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerCashCounts;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerCashCount;

/**
 * @extends Repository<TellerCashCount, TellerCashCounts>
 */
class TellerCashCountRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerCashCount::class, TellerCashCounts::class);
    }
}