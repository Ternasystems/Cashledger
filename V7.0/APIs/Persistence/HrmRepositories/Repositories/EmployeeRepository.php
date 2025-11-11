<?php

namespace API_HrmRepositories;

use API_DTORepositories\Repository;
use API_HrmRepositories_Collection\Employees;
use API_HrmRepositories_Context\HrmContext;
use API_HrmRepositories_Model\Employee;

/**
 * @extends Repository<Employee, Employees>
 */
class EmployeeRepository extends Repository
{
    public function __construct(HrmContext $context)
    {
        parent::__construct($context, Employee::class, Employees::class);
    }
}