<?php

namespace API_Teller_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Teller_Contract\ITellerAuditService;
use API_Teller_Contract\ITellerReversalService;
use API_TellerEntities_Collection\TellerAudits;
use API_TellerEntities_Collection\TellerReversals;
use API_TellerEntities_Model\TellerAudit;
use API_TellerEntities_Model\TellerReversal;
use Exception;

/**
 * This is the Facade class for TellerAudit and TellerReversal management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class TellerAuditFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected ITellerAuditService $auditService, protected ITellerReversalService $reversalService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|TellerAudits|TellerAudit|TellerReversals|TellerReversal
    {
        return match ($resourceType) {
            'TellerAudit' => $this->auditService->getTellerAudits($filter, $page, $pageSize, $reloadMode),
            'TellerReversal' => $this->reversalService->getTellerReversals($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for TellerAuditFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): TellerReversal|TellerAudit
    {
        return match ($resourceType) {
            'TellerAudit' => $this->auditService->setTellerAudit($data),
            'TellerReversal' => $this->reversalService->setTellerReversal($data),
            default => throw new Exception("Invalid resource type for TellerAuditFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): null|TellerReversal|TellerAudit
    {
        return throw new Exception("Invalid or unsupported resource type for TellerAuditFacade 'put': $resourceType");
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for TellerAuditFacade 'delete': $resourceType");
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for TellerAuditFacade 'disable': $resourceType");
    }
}