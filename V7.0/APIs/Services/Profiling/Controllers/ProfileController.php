<?php

namespace API_Profiling_Controller;

use API_Administration_Service\ReloadMode;
use API_Profiling_Contract\ICivilityService;
use API_Profiling_Contract\IGenderService;
use API_Profiling_Contract\IOccupationService;
use API_Profiling_Contract\IProfileService;
use API_Profiling_Contract\IStatusService;
use API_Profiling_Contract\ITitleService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class ProfileController extends BaseController
{
    protected IProfileService $profileService;
    protected ICivilityService $civilityService;
    protected IGenderService $genderService;
    protected IOccupationService $occupationService;
    protected IStatusService $statusService;
    protected ITitleService $titleService;

    public function __construct(IProfileService $profileService, ICivilityService $civilityService, IGenderService $genderService, IOccupationService $occupationService,
                                IStatusService $statusService, ITitleService $titleService)
    {
        $this->profileService = $profileService;
        $this->civilityService = $civilityService;
        $this->genderService = $genderService;
        $this->occupationService = $occupationService;
        $this->statusService = $statusService;
        $this->titleService = $titleService;
    }

    /**
     * A private helper to convert a simple associative array from a URL query
     * into the [column, operator, value] format our repository layer expects.
     *
     * @param array|null $filter e.g., ['Name_like' => 'Eng%']
     * @return array|null e.g., [['Name', 'LIKE', 'Eng%']]
     */
    protected function parseFilter(?array $filter): ?array
    {
        if (is_null($filter)) {
            return null;
        }

        $whereClause = [];
        $operatorMap = ['_like' => 'LIKE', '_gt' => '>', '_gte' => '>=', '_lt' => '<', '_lte' => '<=', '_neq' => '!='];

        foreach ($filter as $key => $value) {
            $column = $key;
            $operator = '='; // Default operator

            foreach ($operatorMap as $suffix => $sqlOperator) {
                if (str_ends_with($key, $suffix)) {
                    $column = substr($key, 0, -strlen($suffix));
                    $operator = $sqlOperator;
                    break;
                }
            }

            $whereClause[] = [$column, $operator, $value];
        }
        return $whereClause;
    }

    /**
     * Gets a paginated list of resources based on the 'controller' parameter.
     * Defaults to getting Profiles.
     *
     * e.g., /?controller=Profile&action=index
     * e.g., /?controller=Gender&action=index&page=1&pageSize=5
     * e.g., /?controller=Status&action=index&filter[Name_like]=Act%
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Profile');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = match ($controller) {
                'Profile' => $this->profileService->getProfiles($filter, $page, $pageSize, $reload),
                'Civility' => $this->civilityService->getCivilities($filter, $page, $pageSize, $reload),
                'Gender' => $this->genderService->getGenders($filter, $page, $pageSize, $reload),
                'Occupation' => $this->occupationService->getOccupations($filter, $page, $pageSize, $reload),
                'Status' => $this->statusService->getStatuses($filter, $page, $pageSize, $reload),
                'Title' => $this->titleService->getTitles($filter, $page, $pageSize, $reload),
                default => throw new Exception('Invalid controller context for index action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single resource by its ID.
     * e.g., /?controller=Profile&action=show&id=profile-uuid-123
     * e.g., /?controller=Gender&action=show&id=gender-uuid-456
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Profile');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];

            $result = match ($controller) {
                'Profile' => $this->profileService->getProfiles($filter, 1, 1, ReloadMode::YES),
                'Civility' => $this->civilityService->getCivilities($filter, 1, 1, ReloadMode::YES),
                'Gender' => $this->genderService->getGenders($filter, 1, 1, ReloadMode::YES),
                'Occupation' => $this->occupationService->getOccupations($filter, 1, 1, ReloadMode::YES),
                'Status' => $this->statusService->getStatuses($filter, 1, 1, ReloadMode::YES),
                'Title' => $this->titleService->getTitles($filter, 1, 1, ReloadMode::YES),
                default => throw new Exception('Invalid controller context for show action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Creates a new resource.
     * Expects a POST request with a JSON body.
     * e.g., /?controller=Profile&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Profile');
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = match ($controller) {
                'Profile' => $this->profileService->setProfile($data),
                'Civility' => $this->civilityService->setCivility($data),
                'Gender' => $this->genderService->setGender($data),
                'Occupation' => $this->occupationService->setOccupation($data),
                'Status' => $this->statusService->setStatus($data),
                'Title' => $this->titleService->setTitle($data),
                default => throw new Exception('Invalid controller context for store action.'),
            };
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing resource.
     * Expects a POST request with a JSON body and an ID in the query string.
     * e.g., /?controller=Profile&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Profile');
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $item = match ($controller) {
                'Profile' => $this->profileService->putProfile($id, $data),
                'Civility' => $this->civilityService->putCivility($id, $data),
                'Gender' => $this->genderService->putGender($id, $data),
                'Occupation' => $this->occupationService->putOccupation($id, $data),
                'Status' => $this->statusService->putStatus($id, $data),
                'Title' => $this->titleService->putTitle($id, $data),
                default => throw new Exception('Invalid controller context for update action.'),
            };

            if (!$item) {
                return $this->json(['error' => 'Resource not found.'], 404);
            }
            return $this->json($item);
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to update resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Deletes (deactivates) a resource by its ID.
     * e.g., /?controller=Profile&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Profile');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = match ($controller) {
                'Profile' => $this->profileService->deleteProfile($id),
                'Civility' => $this->civilityService->deleteCivility($id),
                'Gender' => $this->genderService->deleteGender($id),
                'Occupation' => $this->occupationService->deleteOccupation($id),
                'Status' => $this->statusService->deleteStatus($id),
                'Title' => $this->titleService->deleteTitle($id),
                default => throw new Exception('Invalid controller context for destroy action.'),
            };

            if ($success) {
                return new Response('', 204); // 204 No Content
            }

            return $this->json(['error' => 'Failed to delete resource.'], 500);
        } catch (Exception $e) {
            return $this->json(['error' => 'An exception occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}