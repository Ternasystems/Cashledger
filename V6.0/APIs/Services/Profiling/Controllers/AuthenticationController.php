<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\IAuthenticationService;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class AuthenticationController extends BaseController
{
    private IAuthenticationService $service;

    public function __construct(IAuthenticationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handles a login attempt.
     * Expects a POST request with a JSON body: {"username": "...", "password": "..."}
     */
    public function login(Request $request): Response
    {
        $data = json_decode($request->content, true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $ip = $request->getServer('REMOTE_ADDR');

        if (!$username || !$password) {
            return $this->json(['error' => 'Username and password are required.'], 400); // Bad Request
        }

        $credential = $this->service->authenticate($username, $password, $ip);

        if ($credential) {
            // On success, return the authenticated credential object (which includes the session ID).
            return $this->json($credential);
        }

        // On failure, return a generic error message to prevent user enumeration.
        return $this->json(['error' => 'Invalid credentials.'], 401); // Unauthorized
    }

    /**
     * Handles a logout request.
     * Expects a POST request with a JSON body: {"sessionId": "..."}
     */
    public function logout(Request $request): Response
    {
        $data = json_decode($request->content, true);
        $sessionId = $data['sessionId'] ?? null;

        if (!$sessionId) {
            return $this->json(['error' => 'Session ID is required.'], 400);
        }

        $this->service->deauthenticate($sessionId);

        // A successful logout returns a success message with no content.
        return $this->json(['message' => 'Logout successful.'], 200);
    }

    /**
     * Gets the full session details for the currently authenticated user,
     * including their credential object and a flat list of their permissions.
     * Expects a GET request with a session ID.
     * E.g., /index.php?controller=Authentication&action=session&sessionId=...
     */
    public function session(Request $request): Response
    {
        $sessionId = $request->getQuery('sessionId');

        if (!$sessionId) {
            return $this->json(['error' => 'Session ID is required.'], 400);
        }

        $payload = $this->service->SessionPayload($sessionId);

        if ($payload) {
            return $this->json($payload);
        }

        return $this->json(['error' => 'Invalid or expired session.'], 401); // Unauthorized
    }
}