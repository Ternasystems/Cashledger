<?php

namespace API_BillingRepositories;

use API_BillingRepositories_Context\BillingContext;
use API_BillingRepositories_Model\Currency;
use API_DTORepositories\Repository;

/**
 * @extends Repository<Currency>
 */
class CurrencyRepository extends Repository
{
    public function __construct(BillingContext $context)
    {
        parent::__construct($context, Currency::class);
    }
}