<?php

namespace API_Administration_Controller;

use API_Administration_Contract\IAppService;
use API_Administration_Service\ReloadMode;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class AppController extends BaseController
{
    protected IAppService $service;

    public function __construct(IAppService $service)
    {
        $this->service = $service;
    }

    /**
     * Handles requests for a paginated list of apps.
     * Responds to URLs like: /index.php?controller=App&action=index&page=1&pageSize=10
     */
    public function index(Request $request): Response
    {
        // Get pagination parameters from the request, with sensible defaults.
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('pageSize', 10);
        $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

        // The service now returns a structured array with data and total count.
        $result = $this->service->GetApps(null, $page, $pageSize, $reload);

        return $this->json($result);
    }

    /**
     * Handles requests for a single app by its ID.
     * Responds to URLs like: /index.php?controller=App&action=show&id=app-uuid-123
     */
    public function show(Request $request): Response
    {
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        // Create a filter to find the specific app by its ID.
        $filter = [['Id', '=', $id]];
        $result = $this->service->GetApps($filter, 1, 1, ReloadMode::YES);

        // Return only the single app object, or null if not found.
        $app = $result['data']?->first();

        return $this->json($app);
    }

    /**
     * Handles requests for a list of all app categories.
     * Responds to URLs like: /index.php?controller=App&action=categories
     */
    public function categories(): Response
    {
        $categories = $this->service->GetCategories();
        return $this->json($categories);
    }
}