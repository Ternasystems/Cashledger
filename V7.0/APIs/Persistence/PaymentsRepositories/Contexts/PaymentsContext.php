<?php

namespace API_PaymentsRepositories_Context;

use API_DTORepositories_Context\Context;
use API_PaymentsRepositories_Collection\PaymentMethods;
use API_PaymentsRepositories_Model\PaymentMethod;

/**
 * Acts as a Data Mapper for the Payments DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class PaymentsContext extends Context
{
    // Table name properties, used by the TContext trait via the base Context.
    private string $paymentmethod = 'cl_PaymentMethods';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'paymentmethod' => PaymentMethod::class,
            'paymentmethodcollection' => PaymentMethods::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id'
        ];
    }
}