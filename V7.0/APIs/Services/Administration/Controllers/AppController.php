<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IAppCategoryService;
use API_Administration_Contract\IAppService;
use API_Administration_Service\ReloadMode;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class AppController extends BaseController
{
    protected IAppService $appService;
    protected IAppCategoryService $categoryService;

    public function __construct(IAppService $appService, IAppCategoryService $categoryService)
    {
        $this->appService = $appService;
        $this->categoryService = $categoryService;
    }

    /**
     * Handles requests for a paginated list of apps.
     * Responds to URLs like: /index.php?controller=App&action=index&page=1&pageSize=10
     * Responds to URLs like: /index.php?controller=AppCategory&action=index&page=1&pageSize=10
     */
    public function index(Request $request): Response
    {
        // Get pagination parameters from the request, with sensible defaults.
        $controller = $request->getQuery('controller', 'App');
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        // The service now returns a structured array with data and total count.
        $result = $controller == 'App' ? $this->appService->getApps(null, $page, $pageSize, $reload) : $this->categoryService->getAppCategories(null, $page, $pageSize, $reload);

        return $this->json($result);
    }

    /**
     * Handles requests for a single app by its ID.
     * Responds to URLs like: /index.php?controller=App&action=show&id=app-uuid-123
     * Responds to URLs like: /index.php?controller=AppCategory&action=show&id=app-uuid-123
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'App');
        $id = $request->getQuery('id');
        if (!$id)
            return $this->json(['error' => 'ID parameter is required.'], 400);

        // Create a filter to find the specific app by its ID.
        $filter = [['Id', '=', $id]];
        $result = $controller == 'App' ? $this->appService->getApps($filter, 1, 1, ReloadMode::YES) :
            $this->categoryService->getAppCategories($filter, 1, 1, ReloadMode::YES);

        // Return only the single app object, or null if not found.
        $app = $result['data']?->first();

        return $this->json($app);
    }

    /**
     * Creates a new app or app category.
     * Expects a POST request with a JSON body.
     * /index.php?controller=App&action=store
     * /index.php?controller=AppCategory&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'App');
        $data = json_decode($request->content, true);

        if (!$data)
            return $this->json(['error' => 'Invalid or empty JSON body.'], 400);

        try {
            $newItem = $controller == 'App' ? $this->appService->setApp($data) : $this->categoryService->setAppCategory($data);

            return $this->json($newItem, 201); // 201 Created

        } catch (Exception $e) {
            // Log the exception in a real application
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing app or app category.
     * Expects a POST/PUT request with a JSON body and an ID in the query string.
     * /index.php?controller=App&action=update&id=app-uuid-123
     * /index.php?controller=AppCategory&action=update&id=cat-uuid-456
     */
    public function update(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'App');
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data)
            return $this->json(['error' => 'ID parameter and JSON body are required.'], 400);

        try {
            $updatedItem = $controller == 'App' ? $this->appService->putApp($id, $data) : $this->categoryService->putAppCategory($id, $data);

            if (!$updatedItem)
                return $this->json(['error' => 'Resource not found.'], 404);

            return $this->json($updatedItem, 200); // 200 OK

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) an app or app category by its ID.
     * /index.php?controller=App&action=destroy&id=app-uuid-123
     * /index.php?controller=AppCategory&action=destroy&id=cat-uuid-456
     */
    public function destroy(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'App');
        $id = $request->getQuery('id');

        if (!$id)
            return $this->json(['error' => 'ID parameter is required.'], 400);

        try {
            $success = $controller == 'App' ? $this->appService->deleteApp($id) : $this->categoryService->deleteAppCategory($id);

            if ($success)
                return new Response('', 204); // 204 No Content

            return $this->json(['error' => 'Failed to delete resource.'], 500);

        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}