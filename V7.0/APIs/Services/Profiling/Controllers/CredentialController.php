<?php

namespace API_Profiling_Controller;

use API_Administration_Controller\AbstractController;
use API_Profiling_Facade\CredentialFacade;
use Exception;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * The concrete CredentialController.
 * It extends the AbstractController and is now extremely simple.
 */
class CredentialController extends AbstractController
{
    protected CredentialFacade $credentialFacade;
    /**
     * This tells the parent class what the default resource type is
     * if the 'controller' query param is not provided.
     */
    protected string $defaultResourceType = 'Credential';

    /**
     * We inject our specific CredentialFacade for type safety.
     * We then pass it up to the parent constructor.
     */
    public function __construct(CredentialFacade $facade)
    {
        parent::__construct($facade);
        $this->credentialFacade = $facade;
    }

    /**
     * Updates or Resets a credential's password.
     * If 'password' is in the JSON body, it updates.
     * If 'password' is not present, it resets.
     * e.g., /?controller=Credential&action=password&id=...
     */
    public function password(Request $request): Response
    {
        $resourceType = $this->getResourceType($request);
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'Credential ID is required.'], 400);
        }

        $data = json_decode($request->content, true);
        $newPassword = $data['password'] ?? null;

        try {
            $success = $this->credentialFacade->putPassword($resourceType, $id, $newPassword);
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