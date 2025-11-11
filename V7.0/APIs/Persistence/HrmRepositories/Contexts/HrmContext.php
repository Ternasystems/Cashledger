<?php

namespace API_HrmRepositories_Context;

use API_DTORepositories_Context\Context;
use API_HrmRepositories_Collection\Employees;
use API_HrmRepositories_Model\Employee;

/**
 * Acts as a Data Mapper for the HRM DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class HrmContext extends Context
{
    // Table name properties specific to this context.
    private string $employee = 'cl_Employees';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'employee' => Employee::class,
            'employeecollection' => Employees::class
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