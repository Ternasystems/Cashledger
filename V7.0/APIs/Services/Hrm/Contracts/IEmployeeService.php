<?php

namespace API_Hrm_Contract;

use API_Administration_Service\ReloadMode;
use API_HrmEntities_Collection\Employees;
use API_HrmEntities_Model\Employee;

interface IEmployeeService
{
    /**
     * Gets a paginated list of Employee entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Employee|Employees|null An associative array containing 'data' and 'total'.
     */
    public function getEmployees(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Employee|Employees|null;

    /**
     * Creates a new Employee and assigns roles.
     *
     * @param array $data
     * @return Employee The newly created Employee entity.
     */
    public function setEmployee(array $data): Employee;

    /**
     * Updates an existing Employee
     *
     * @param string $id
     * @param array $data
     * @return Employee|null
     */
    public function putEmployee(string $id, array $data): ?Employee;

    /**
     * Deletes an Employee and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteEmployee(string $id): bool;

    /**
     * Disable an Employee and its associated role relations
     *
     * @param string $id
     * @return bool
     */
    public function disableEmployee(string $id): bool;
}