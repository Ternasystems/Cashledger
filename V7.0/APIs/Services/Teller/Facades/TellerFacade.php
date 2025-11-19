<?php

namespace API_Teller_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ITellerService;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Model\Teller;
use Exception;

/**
 * This is an "Adapter Facade" for the TellerService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ITellerService (getTellers, setTeller, etc.).
 */
class TellerFacade implements IFacade
{

    public function __construct(protected ITellerService $tellerService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles tellers.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Tellers|Teller
    {
        return $this->tellerService->getTellers($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Teller
    {
        return $this->tellerService->setTeller($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Teller
    {
        return $this->tellerService->putTeller($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->tellerService->deleteTeller($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return $this->tellerService->disableTeller($id);
    }
}