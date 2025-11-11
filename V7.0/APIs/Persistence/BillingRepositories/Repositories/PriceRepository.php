<?php

namespace API_BillingRepositories;

use API_BillingRepositories_Collection\Prices;
use API_BillingRepositories_Context\BillingContext;
use API_BillingRepositories_Model\Price;
use API_DTORepositories\Repository;

/**
 * @extends Repository<Price, Prices>
 */
class PriceRepository extends Repository
{
    public function __construct(BillingContext $context)
    {
        parent::__construct($context, Price::class, Prices::class);
    }
}