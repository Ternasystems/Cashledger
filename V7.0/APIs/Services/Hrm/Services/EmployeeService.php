<?php

namespace API_Hrm_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\HrmException;
use API_Hrm_Contract\IEmployeeService;
use API_HrmEntities_Collection\Employees;
use API_HrmEntities_Factory\EmployeeFactory;
use API_HrmEntities_Model\Employee;
use Throwable;
use TS_Exception\Classes\DomainException;

class EmployeeService implements IEmployeeService
{
    protected EmployeeFactory $employeeFactory;
    protected Employees $employees;

    public function __construct(EmployeeFactory $employeeFactory)
    {
        $this->employeeFactory = $employeeFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getEmployees(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Employee|Employees|null
    {
        if (!isset($this->employees) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->employeeFactory->filter($filter, $pageSize, $offset);
            $this->employeeFactory->Create();
            $this->employees = $this->employeeFactory->collectable();
        }

        if (count($this->employees) === 0)
            return null;

        return $this->employees->count() > 1 ? $this->employees : $this->employees->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function setEmployee(array $data): Employee
    {
        $context = $this->employeeFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main employee DTO
            $employee = new \API_HrmRepositories_Model\Employee($data['employeeData']);
            $this->employeeFactory->repository()->add($employee);

            // 2. Get the newly created employee
            $employee = $this->employeeFactory->repository()->first([['ProfileId', '=', $data['employeeData']['ProfileId']]]);
            if (!$employee)
                throw new HrmException('employee_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getEmployees([['Id', '=', $employee->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function putEmployee(string $id, array $data): ?Employee
    {
        $context = $this->employeeFactory->repository()->context;
        $context->beginTransaction();

        try{
            $employee = $this->getEmployees([['Id', '=', $id]])?->first();
            if (!$employee)
                throw new HrmException('employee_not_found', ["Id" => $id]);

            // 1. Update the main employee record
            foreach ($data as $field => $value)
                $employee->it()->{$field} = $value ?? $employee->it()->{$field};

            $this->employeeFactory->repository()->update($employee->it());
            $context->commit();

            return $this->getEmployees([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteEmployee(string $id): bool
    {
        $context = $this->employeeFactory->repository()->context;
        $context->beginTransaction();

        try{
            $employee = $this->getEmployees([['Id', '=', $id]])?->first();
            if (!$employee){
                $context->commit();
                return true;
            }

            $this->employeeFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function disableEmployee(string $id): bool
    {
        $context = $this->employeeFactory->repository()->context;
        $context->beginTransaction();

        try{
            $employee = $this->getEmployees([['Id', '=', $id]])?->first();
            if (!$employee){
                $context->commit();
                return true;
            }

            $this->employeeFactory->repository()->deactivate($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}