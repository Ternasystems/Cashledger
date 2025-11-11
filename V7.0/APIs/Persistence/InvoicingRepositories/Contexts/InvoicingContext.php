<?php

namespace API_InvoicingRepositories_Context;

use API_DTORepositories_Context\Context;
use API_InvoicingRepositories_Collection\Customers;
use API_InvoicingRepositories_Model\Customer;

/**
 * Acts as a Data Mapper for the Invoicing DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class InvoicingContext extends Context
{
    // Table name properties specific to this context.
    private string $customer = 'cl_Customers';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'customer' => Customer::class,
            'customercollection' => Customers::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'ProfileID' => 'ProfileId',
        ];
    }
}