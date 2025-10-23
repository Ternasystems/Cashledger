<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\IContactService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class ContactController extends BaseController
{
    private IContactService $service;

    public function __construct(IContactService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of contacts.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter'); // Expects an array like ['ProfileId' => 'some-uuid']

        $result = $this->service->getContacts($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Gets a paginated list of all available contact types.
     */
    public function types(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $result = $this->service->getContactTypes(null, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new contact.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $contact = $this->service->SetContact($data);
            return $this->json($contact, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create contact.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing contact.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Contact&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $contact = $this->service->PutContact($id, $data);
            if (!$contact) {
                return $this->json(['error' => 'Contact not found.'], 404);
            }
            return $this->json($contact);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update contact.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a contact by its ID.
     * e.g., /?controller=Contact&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        $success = $this->service->DeleteContact($id);

        if ($success) {
            return new Response('', 204); // 204 No Content
        }

        return $this->json(['error' => 'Failed to delete contact.'], 500);
    }
}