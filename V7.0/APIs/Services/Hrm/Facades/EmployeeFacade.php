<?php

namespace API_Hrm_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Hrm_Contract\IEmployeeService;
use API_HrmEntities_Collection\Employees;
use API_HrmEntities_Model\Employee;
use Exception;

/**
 * This is an "Adapter Facade" for the EmployeeService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IEmployeeService (getEmployees, setEmployee, etc.).
 */
class EmployeeFacade implements IFacade
{
    public function __construct(protected IEmployeeService $employeeService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles employees.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Employees|Employee
    {
        return $this->employeeService->getEmployees($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Employee
    {
        return $this->employeeService->setEmployee($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Employee
    {
        return $this->employeeService->putEmployee($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->employeeService->deleteEmployee($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return $this->employeeService->disableEmployee($id);
    }
}