<?php

namespace API_Profiling_Controller;

use API_Administration_Service\ReloadMode;
use API_Profiling_Contract\IContactService;
use API_Profiling_Contract\IContactTypeService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class ContactController extends BaseController
{
    private IContactService $contactService;
    private IContactTypeService $contactTypeService;

    public function __construct(IContactService $contactService, IContactTypeService $contactTypeService)
    {
        $this->contactService = $contactService;
        $this->contactTypeService = $contactTypeService;
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
     * Gets a paginated list of contacts OR contact types.
     * Supports filtering via query string.
     * e.g., /?controller=Contact&action=index&filter[ProfileId]=...
     * e.g., /?controller=ContactType&action=index&filter[Name_like]=Email%
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Contact');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            // Dynamically call the correct service based on the controller parameter
            $result = match ($controller) {
                'Contact' => $this->contactService->getContacts($filter, $page, $pageSize, $reload),
                'ContactType' => $this->contactTypeService->getContactTypes($filter, $page, $pageSize, $reload),
                default => throw new Exception('Invalid controller context for index action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single contact or contact type by its ID.
     * Responds to URLs like:
     * /index.php?controller=Contact&action=show&id=contact-uuid-123
     * /index.php?controller=ContactType&action=show&id=type-uuid-456
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Contact');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        // Create a filter to find the specific item by its ID.
        $filter = [['Id', '=', $id]];

        if ($controller === 'Contact') {
            $result = $this->contactService->getContacts($filter, 1, 1, ReloadMode::YES);
        } else if ($controller === 'ContactType') {
            $result = $this->contactTypeService->getContactTypes($filter, 1, 1, ReloadMode::YES);
        } else {
            return $this->json(['error' => 'Invalid controller context for show action.'], 400);
        }

        return $this->json($result);
    }

    /**
     * Creates a new contact or contact type.
     * Expects a POST request with a JSON body.
     * /index.php?controller=Contact&action=store
     * /index.php?controller=ContactType&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Contact');
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = match ($controller) {
                'Contact' => $this->contactService->setContact($data),
                'ContactType' => $this->contactTypeService->setContactType($data),
                default => throw new Exception('Invalid controller context for store action.'),
            };
            return $this->json($item, 201); // 201 Created
        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to create resource.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Updates an existing contact or contact type.
     * Expects a POST request with a JSON body. The ID comes from the URL.
     * e.g., /?controller=Contact&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Contact');
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $item = match ($controller) {
                'Contact' => $this->contactService->putContact($id, $data),
                'ContactType' => $this->contactTypeService->putContactType($id, $data),
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
     * Deletes a contact or contact type by its ID.
     * e.g., /?controller=Contact&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Contact');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = match ($controller) {
                'Contact' => $this->contactService->deleteContact($id),
                'ContactType' => $this->contactTypeService->deleteContactType($id),
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