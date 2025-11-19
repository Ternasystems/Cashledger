<?php

namespace API_Teller_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ICashFigureService;
use API_TellerEntities_Collection\CashFigures;
use API_TellerEntities_Model\CashFigure;
use Exception;

/**
 * This is an "Adapter Facade" for the CashFigureService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ICashFigureService (getCashFigures, setCashFigure, etc.).
 */
class CashFigureFacade implements IFacade
{
    public function __construct(protected ICashFigureService $cashFigureService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles cashFigures.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|CashFigures|CashFigure
    {
        return $this->cashFigureService->getCashFigures($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): CashFigure
    {
        return $this->cashFigureService->setCashFigure($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?CashFigure
    {
        return $this->cashFigureService->putCashFigure($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->cashFigureService->deleteCashFigure($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The ICashFigureService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for CashFigureFacade");
    }
}