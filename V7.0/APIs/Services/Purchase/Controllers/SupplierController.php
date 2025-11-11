<?php

namespace API_Purchase_Controller;

use API_Purchase_Contract\ISupplierService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class SupplierController extends BaseController
{
    private ISupplierService $service;

    public function __construct(ISupplierService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of suppliers.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter');

        $result = $this->service->getSuppliers($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new supplier.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $supplier = $this->service->SetSupplier($data);
            return $this->json($supplier, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create supplier.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing supplier.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Supplier&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $supplier = $this->service->PutSupplier($id, $data);
            if (!$supplier) {
                return $this->json(['error' => 'Supplier not found.'], 404);
            }
            return $this->json($supplier);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update supplier.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a supplier by its ID (soft delete).
     * e.g., /?controller=Supplier&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->DeleteSupplier($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete supplier.'], 500);
        } catch(Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}