<?php

namespace API_PurchaseRepositories_Context;

use API_DTORepositories_Context\Context;
use API_PurchaseRepositories_Collection\Suppliers;
use API_PurchaseRepositories_Model\Supplier;

/**
 * Acts as a Data Mapper for the Purchase DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class PurchaseContext extends Context
{
    // Table name properties specific to this context.
    private string $supplier = 'cl_Suppliers';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'supplier' => Supplier::class,
            'suppliercollection' => Suppliers::class
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