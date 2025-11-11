<?php

namespace API_BillingRepositories;

use API_BillingRepositories_Collection\Discounts;
use API_BillingRepositories_Context\BillingContext;
use API_BillingRepositories_Model\Discount;
use API_DTORepositories\Repository;

/**
 * @extends Repository<Discount, Discounts>
 */
class DiscountRepository extends Repository
{
    public function __construct(BillingContext $context)
    {
        parent::__construct($context, Discount::class, Discounts::class);
    }
}