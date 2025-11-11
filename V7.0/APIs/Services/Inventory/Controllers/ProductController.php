<?php

namespace API_Inventory_Controller;

use API_Administration_Service\ReloadMode;
use API_Inventory_Contract\IPackagingService;
use API_Inventory_Contract\IProductService;
use API_Inventory_Contract\IUnitService;
use Exception;
use TS_Controller\Classes\BaseController;
use TS_Http\Classes\Request;
use TS_Http\Classes\Response;

class ProductController extends BaseController
{
    protected IProductService $productService;
    protected IPackagingService $packagingService;
    protected IUnitService $unitService;

    public function __construct(IProductService $productService, IPackagingService $packagingService, IUnitService $unitService)
    {
        $this->productService = $productService;
        $this->packagingService = $packagingService;
        $this->unitService = $unitService;
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
     * Defaults to getting Products.
     *
     * e.g., /?controller=Product&action=index
     * e.g., /?controller=Unit&action=index&page=1&pageSize=5
     * e.g., /?controller=Packaging&action=index&filter[Name_like]=Box%
     */
    public function index(Request $request): Response
    {
        try {
            $controller = $request->getQuery('controller', 'Product');
            $page = (int)$request->getQuery('page', 1);
            $pageSize = (int)$request->getQuery('pageSize', 10);
            $reload = $request->getQuery('reload', 'No') === 'Yes' ? ReloadMode::YES : ReloadMode::NO;

            $filterParams = $request->getQuery('filter');
            $filter = $this->parseFilter($filterParams);

            $result = match ($controller) {
                'Product' => $this->productService->getProducts($filter, $page, $pageSize, $reload),
                'Packaging' => $this->packagingService->getPackagings($filter, $page, $pageSize, $reload),
                'Unit' => $this->unitService->getUnits($filter, $page, $pageSize, $reload),
                default => throw new Exception('Invalid controller context for index action.'),
            };

            return $this->json($result);

        } catch (Exception $e) {
            return $this->json(['error' => 'Failed to retrieve resources.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles requests for a single resource by its ID.
     * e.g., /?controller=Product&action=show&id=product-uuid-123
     * e.g., /?controller=Unit&action=show&id=unit-uuid-456
     */
    public function show(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Product');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID parameter is required.'], 400);
        }

        try {
            $filter = [['Id', '=', $id]];

            $result = match ($controller) {
                'Product' => $this->productService->getProducts($filter, 1, 1, ReloadMode::YES),
                'Packaging' => $this->packagingService->getPackagings($filter, 1, 1, ReloadMode::YES),
                'Unit' => $this->unitService->getUnits($filter, 1, 1, ReloadMode::YES),
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
     * e.g., /?controller=Product&action=store
     */
    public function store(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Product');
        $data = json_decode($request->content, true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON body.'], 400);
        }

        try {
            $item = match ($controller) {
                'Product' => $this->productService->SetProduct($data),
                'Packaging' => $this->packagingService->SetPackaging($data),
                'Unit' => $this->unitService->SetUnit($data),
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
     * e.g., /?controller=Product&action=update&id=...
     */
    public function update(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Product');
        $id = $request->getQuery('id');
        $data = json_decode($request->content, true);

        if (!$id || !$data) {
            return $this->json(['error' => 'ID and JSON body are required.'], 400);
        }

        try {
            $item = match ($controller) {
                'Product' => $this->productService->PutProduct($id, $data),
                'Packaging' => $this->packagingService->PutPackaging($id, $data),
                'Unit' => $this->unitService->PutUnit($id, $data),
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
     * e.g., /?controller=Product&action=destroy&id=...
     */
    public function destroy(Request $request): Response
    {
        $controller = $request->getQuery('controller', 'Product');
        $id = $request->getQuery('id');
        if (!$id) {
            return $this->json(['error' => 'ID is required.'], 400);
        }

        try {
            $success = match ($controller) {
                'Product' => $this->productService->DeleteProduct($id),
                'Packaging' => $this->packagingService->DeletePackaging($id),
                'Unit' => $this->unitService->DeleteUnit($id),
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