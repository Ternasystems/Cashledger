<?php

namespace API_TaxesRepositories_Context;

use API_DTORepositories_Context\Context;
use API_TaxesRepositories_Collection\Taxes;
use API_TaxesRepositories_Model\Tax;

/**
 * Acts as a Data Mapper for the Taxes DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class TaxesContext extends Context
{
    // Table name properties specific to this context.
    private string $tax = 'cl_Taxes';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'tax' => Tax::class,
            'taxcollection' => Taxes::class
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