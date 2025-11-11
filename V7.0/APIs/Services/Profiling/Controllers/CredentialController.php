<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\ICredentialService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class CredentialController extends BaseController
{
    private ICredentialService $service;

    public function __construct(ICredentialService $service)
    {
        $this->service = $service;
    }

    /**
     * Gets a paginated list of credentials.
     * Supports filtering via query string, e.g., ?filter[ProfileId]=...
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $filter = $request->getQuery('filter');

        $result = $this->service->getCredentials($filter, $page, $pageSize);
        return $this->json($result);
    }

    /**
     * Creates a new credential.
     * Expects a POST request with a JSON body.
     */
    public function store(Request $request): Response
    {
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $credential = $this->service->setCredential($data);
            return $this->json($credential, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create credential.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing credential.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Credential&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $credential = $this->service->putCredential($id, $data);
            if (!$credential) {
                return $this->json(['error' => 'Credential not found.'], 404);
            }
            return $this->json($credential);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update credential.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes a credential by its ID (soft delete).
     * e.g., /?controller=Credential&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = $this->service->deleteCredential($id);
            if ($success) {
                return new Response('', 204); // 204 No Content
            }
            return $this->json(['error' => 'Failed to delete credential.'], 500);
        } catch(Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates or Resets a credential's password.
     * If 'password' is in the JSON body, it updates.
     * If 'password' is not present, it resets.
     * e.g., /?controller=Credential&action=password&id=...
     */
    public function password(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'Credential ID is required.'], 400);
        }

        $data = json_decode($request->content, true);
        $newPassword = $data['password'] ?? null;

        try {
            $success = $this->service->putPassword($id, $newPassword);
            if ($success) {
                $message = $newPassword ? 'Password updated successfully.' : 'Password reset successfully.';
                return $this->json(['success' => true, 'message' => $message]);
            }
            return $this->json(['error' => 'Operation failed.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update password.', 'message' => $e->getMessage()], 500);
        }
    }
}