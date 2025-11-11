<?php

namespace API_Invoicing_Controller;

use API_Invoicing_Contract\ICustomerService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CustomerController extends BaseController
{
    private ICustomerService $service;

    public function __construct(ICustomerService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of customers.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter');

        $result = $this->service->getCustomers($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new customer.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $customer = $this->service->SetCustomer($data);
            return $this->json($customer, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create customer.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing customer.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Customer&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $customer = $this->service->PutCustomer($id, $data);
            if (!$customer) {
                return $this->json(['error' => 'Customer not found.'], 404);
            }
            return $this->json($customer);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update customer.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a customer by its ID (soft delete).
     * e.g., /?controller=Customer&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->DeleteCustomer($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete customer.'], 500);
        } catch(Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}