<?php

namespace API_Inventory_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\Stock;
use Exception;

/**
 * This is an "Adapter Facade" for the StockService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IStockService (getStocks, setStock, etc.).
 */
class StockFacade implements IFacade
{
    public function __construct(protected IStockService $stockService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles stocks.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Stocks|Stock
    {
        return $this->stockService->getStocks($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Stock
    {
        return $this->stockService->setStock($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Stock
    {
        return $this->stockService->putStock($id, $data);
    }

    public function putQuantity(string $resourceType, string $id, float $quantity): ?Stock
    {
        return $this->stockService->putQuantity($id, $quantity);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->stockService->deleteStock($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The IStockService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for StockFacade");
    }
}