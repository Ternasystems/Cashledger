<?php

namespace API_BillingRepositories_Context;

use API_BillingRepositories_Collection\Currencies;
use API_BillingRepositories_Collection\Discounts;
use API_BillingRepositories_Collection\Prices;
use API_BillingRepositories_Model\Currency;
use API_BillingRepositories_Model\Discount;
use API_BillingRepositories_model\Price;
use API_DTORepositories_Context\Context;

class BillingContext extends Context
{
    // Table name properties, used by the TContext trait via the base Context.
    private string $currency = 'cl_Currencies';
    private string $discount = 'cl_Discounts';
    private string $price = 'cl_Prices';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'currency' => Currency::class,
            'discount' => Discount::class,
            'price' => Price::class,
            'currencycollection' => Currencies::class,
            'discountcollection' => Discounts::class,
            'pricecollection' => Prices::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'CurrencyID' => 'CurrencyId'
        ];
    }
}