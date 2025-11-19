<?php

namespace API_Inventory_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Inventory_Contract\IPackagingService;
use API_Inventory_Contract\IProductService;
use API_Inventory_Contract\IUnitService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Packaging;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\Unit;
use Exception;

/**
 * This is the Facade class for Product, Packaging, and Unit management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class ProductFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected IProductService $productService, protected IPackagingService $packagingService, protected IUnitService $unitService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Products|Product|Packagings|Packaging|Units|Unit
    {
        return match ($resourceType) {
            'Product' => $this->productService->getProducts($filter, $page, $pageSize, $reloadMode),
            'Packaging' => $this->packagingService->getPackagings($filter, $page, $pageSize, $reloadMode),
            'Unit' => $this->unitService->getUnits($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for ProductFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): Unit|Product|Packaging
    {
        return match ($resourceType) {
            'Product' => $this->productService->SetProduct($data),
            'Packaging' => $this->packagingService->SetPackaging($data),
            'Unit' => $this->unitService->SetUnit($data),
            default => throw new Exception("Invalid resource type for ProductFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): null|Unit|Product|Packaging
    {
        return match ($resourceType) {
            'Product' => $this->productService->PutProduct($id, $data),
            'Packaging' => $this->packagingService->PutPackaging($id, $data),
            'Unit' => $this->unitService->PutUnit($id, $data),
            default => throw new Exception("Invalid resource type for ProductFacade 'put': $resourceType"),
        };
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return match ($resourceType) {
            'Product' => $this->productService->DeleteProduct($id),
            'Packaging' => $this->packagingService->DeletePackaging($id),
            'Unit' => $this->unitService->DeleteUnit($id),
            default => throw new Exception("Invalid resource type for ProductFacade 'delete': $resourceType"),
        };
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for ProductFacade 'disable': $resourceType");
    }
}