<?php

namespace API_Payments_Controller;

use API_Payments_Contract\IPaymentMethodService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class PaymentController extends BaseController
{
    private IPaymentMethodService $service;

    public function __construct(IPaymentMethodService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of PaymentMethods.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter');

        $result = $this->service->getPaymentMethods($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new paymentMethod.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $paymentMethod = $this->service->SetPaymentMethod($data);
            return $this->json($paymentMethod, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create paymentMethod.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing paymentMethod.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=PaymentMethod&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $paymentMethod = $this->service->PutPaymentMethod($id, $data);
            if (!$paymentMethod) {
                return $this->json(['error' => 'PaymentMethod not found.'], 404);
            }
            return $this->json($paymentMethod);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update paymentMethod.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a paymentMethod by its ID (soft delete).
     * e.g., /?controller=PaymentMethod&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->DeletePaymentMethod($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete paymentMethod.'], 500);
        } catch(Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}