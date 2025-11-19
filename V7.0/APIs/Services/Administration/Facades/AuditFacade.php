<?php

namespace API_Administration_Facade;

use API_Administration_Contract\IAuditService;
use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Audits;
use API_DTOEntities_Model\Audit;
use Exception;

/**
 * This is an "Adapter Facade" for the AuditService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on IAuditService (getAudits, setAudit, etc.).
 */
class AuditFacade implements IFacade
{
    public function __construct(protected IAuditService $auditService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles audits.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Audits|Audit
    {
        return $this->auditService->getAudits($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): mixed
    {
        // The IAuditService doesn't have a 'set' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'set' for AuditFacade");
    }

    /**
     * Updates an existing resource.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): mixed
    {
        // The IAuditService doesn't have a 'put' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'put' for AuditFacade");
    }

    /**
     * Deletes (soft) a resource.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        // The IAuditService doesn't have a 'delete' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'delete' for AuditFacade");
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The IAuditService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for AuditFacade");
    }
}