<?php

namespace API_Hrm_Controller;

use API_Hrm_Contract\IEmployeeService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class EmployeeController extends BaseController
{
    private IEmployeeService $service;

    public function __construct(IEmployeeService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of employees.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter');

        $result = $this->service->getEmployees($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new employee.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $employee = $this->service->SetEmployee($data);
            return $this->json($employee, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create employee.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing employee.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Employee&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $employee = $this->service->PutEmployee($id, $data);
            if (!$employee) {
                return $this->json(['error' => 'Employee not found.'], 404);
            }
            return $this->json($employee);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update employee.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a employee by its ID (soft delete).
     * e.g., /?controller=Employee&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->DeleteEmployee($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete employee.'], 500);
        } catch(Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}