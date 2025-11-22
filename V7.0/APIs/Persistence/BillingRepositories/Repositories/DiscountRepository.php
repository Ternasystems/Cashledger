<?php

namespace API_BillingRepositories;

use API_BillingRepositories_Context\BillingContext;
use API_BillingRepositories_Model\Discount;
use API_DTORepositories\Repository;

/**
 * @extends Repository<Discount>
 */
class DiscountRepository extends Repository
{
    public function __construct(BillingContext $context)
    {
        parent::__construct($context, Discount::class);
    }
}