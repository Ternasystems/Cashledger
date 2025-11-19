<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

/**
 * An abstract controller that provides default CRUD implementations
 * for any controller that is fronting a facade.
 *
 * This is the "Repository" pattern equivalent for our API controllers.
 */
class AbstractController extends BaseController
{
    /**
     * The default resource type (e.g., 'App', 'Country', 'Product').
     * Concrete controllers MUST override this property.
     */
    protected string $defaultResourceType = '';

    /**
     * The constructor accepts the generic IFacade from the child controller.
     */
    public function __construct(protected IFacade $facade){}

    /**
     * Gets the resource type to operate on.
     * It defaults to the 'controller' query parameter, but
     * falls back to the $defaultResourceType set by the child class.
     */
    protected function getResourceType(Request $request): string
    {
        return $request->getQuery('controller', $this->defaultResourceType);
    }

    /**
     * Handles requests for a paginated list of resources.
     */
    public function index(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams); // Inherited from BaseController

            $result = $this->facade->get($resourceType, $filter, $page, $pageSize, $reload);

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => "Failed to retrieve $resourceType resources.", 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single resource by its ID.
     */
    public function show(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $id = $request->getQuery('id');
            if (!$id) {
                return $this->json(['error' => 'ID parameter is required.'], 400);
            }

            $filter = [['Id', '=', $id]];
            $result = $this->facade->get($resourceType, $filter, 1, 1, ReloadMode::YES);

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => "Failed to retrieve $resourceType resource.", 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new resource.
     */
    public function store(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $data = json_decode($request->content, true);

            if (!$data) {
                return $this->json(['error' => 'Invalid or empty JSON body.'], 400);
            }

            $newItem = $this->facade->set($resourceType, $data);

            return $this->json($newItem, 201); // 201 Created

        } catch (Exception $e) {
            return $this->json(['error' => "Failed to create $resourceType resource.", 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing resource.
     */
    public function update(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $id = $request->getQuery('id');
            $data = json_decode($request->content, true);

            if (!$id || !$data) {
                return $this->json(['error' => 'ID parameter and JSON body are required.'], 400);
            }

            $updatedItem = $this->facade->put($resourceType, $id, $data);

            if (!$updatedItem) {
                return $this->json(['error' => 'Resource not found.'], 404);
            }

            return $this->json($updatedItem, 200); // 200 OK

        } catch (Exception $e) {
            return $this->json(['error' => "Failed to update $resourceType resource.", 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a resource by its ID.
     */
    public function destroy(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $id = $request->getQuery('id');

            if (!$id) {
                return $this->json(['error' => 'ID parameter is required.'], 400);
            }

            $success = $this->facade->delete($resourceType, $id);

            if ($success) {
                return new Response('', 204); // 204 No Content
            }

            return $this->json(['error' => "Failed to delete $resourceType resource."], 500);

        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Disables (deactivates) a resource by its ID.
     * This is a separate action from 'destroy' (soft delete).
     */
    public function disable(Request $request): Response
    {
        try {
            $resourceType = $this->getResourceType($request);
            $id = $request->getQuery('id');

            if (!$id) {
                return $this->json(['error' => 'ID parameter is required.'], 400);
            }

            $success = $this->facade->disable($resourceType, $id);

            if ($success) {
                return $this->json(['success' => true, 'message' => "$resourceType disabled successfully."]);
            }

            return $this->json(['error' => "Failed to disable $resourceType resource."], 500);

        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}