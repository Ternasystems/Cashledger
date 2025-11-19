<?php

namespace API_Administration_Facade;

use API_Administration_Contract\IAppCategoryService;
use API_Administration_Contract\IAppService;
use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Model\App;
use API_DTOEntities_Model\AppCategory;
use Exception;

/**
 * This is the Facade class for App and AppCategory management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class AppFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected IAppService $appService, protected IAppCategoryService $categoryService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Apps|App|AppCategories|AppCategory
    {
        return match ($resourceType) {
            'App' => $this->appService->getApps($filter, $page, $pageSize, $reloadMode),
            'AppCategory' => $this->categoryService->getAppCategories($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for AppFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): AppCategory|App
    {
        return match ($resourceType) {
            'App' => $this->appService->setApp($data),
            'AppCategory' => $this->categoryService->setAppCategory($data),
            default => throw new Exception("Invalid resource type for AppFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): null|AppCategory|App
    {
        return match ($resourceType) {
            'App' => $this->appService->putApp($id, $data),
            'AppCategory' => $this->categoryService->putAppCategory($id, $data),
            default => throw new Exception("Invalid resource type for AppFacade 'put': $resourceType"),
        };
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return match ($resourceType) {
            'App' => $this->appService->deleteApp($id),
            'AppCategory' => $this->categoryService->deleteAppCategory($id),
            default => throw new Exception("Invalid resource type for AppFacade 'delete': $resourceType"),
        };
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for AppFacade 'disable': $resourceType");
    }
}