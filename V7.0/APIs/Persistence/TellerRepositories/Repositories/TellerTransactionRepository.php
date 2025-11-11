<?php

namespace API_TellerRepositories;

use API_DTORepositories\Repository;
use API_TellerRepositories_Collection\TellerTransactions;
use API_TellerRepositories_Context\TellerContext;
use API_TellerRepositories_Model\TellerTransaction;

/**
 * @extends Repository<TellerTransaction, TellerTransactions>
 */
class TellerTransactionRepository extends Repository
{
    public function __construct(TellerContext $context)
    {
        parent::__construct($context, TellerTransaction::class, TellerTransactions::class);
    }
}