<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerReceipt;

/**
 * @extends Repository<TellerReceipt>
 */
class TellerReceiptRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerReceipt::class);
    }
}