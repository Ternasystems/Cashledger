<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerTransfers;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerTransfer;

/**
 * @extends Repository<TellerTransfer, TellerTransfers>
 */
class TellerTransferRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerTransfer::class, TellerTransfers::class);
    }
}