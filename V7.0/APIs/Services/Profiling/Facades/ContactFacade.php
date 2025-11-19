<?php

namespace API_Profiling_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Service\ReloadMode;
use API_Profiling_Contract\IContactService;
use API_Profiling_Contract\IContactTypeService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;
use Exception;

/**
 * This is the Facade class for Contact and ContactType management.
 * It implements the generic IFacade interface directly.
 * It injects the individual services so controllers don't have to.
 */
class ContactFacade implements IFacade
{
    /**
     * The constructor injects all the individual services
     * this facade will orchestrate.
     */
    public function __construct(protected IContactService $contactService, protected IContactTypeService $contactTypeService) {}

    /**
     * Gets a resource from the appropriate service.
     * @throws Exception
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Contacts|Contact|ContactTypes|ContactType
    {
        return match ($resourceType) {
            'Contact' => $this->contactService->getContacts($filter, $page, $pageSize, $reloadMode),
            'ContactType' => $this->contactTypeService->getContactTypes($filter, $page, $pageSize, $reloadMode),
            default => throw new Exception("Invalid resource type for ContactFacade 'get': $resourceType"),
        };
    }

    /**
     * Creates a new resource using the appropriate service.
     * @throws Exception
     */
    public function set(string $resourceType, array $data): ContactType|Contact
    {
        return match ($resourceType) {
            'Contact' => $this->contactService->setContact($data),
            'ContactType' => $this->contactTypeService->setContactType($data),
            default => throw new Exception("Invalid resource type for ContactFacade 'set': $resourceType"),
        };
    }

    /**
     * Updates an existing resource using the appropriate service.
     * @throws Exception
     */
    public function put(string $resourceType, string $id, array $data): null|ContactType|Contact
    {
        return match ($resourceType) {
            'Contact' => $this->contactService->putContact($id, $data),
            'ContactType' => $this->contactTypeService->putContactType($id, $data),
            default => throw new Exception("Invalid resource type for ContactFacade 'put': $resourceType"),
        };
    }

    /**
     * Deletes (soft) a resource using the appropriate service.
     * @throws Exception
     */
    public function delete(string $resourceType, string $id): bool
    {
        return match ($resourceType) {
            'Contact' => $this->contactService->deleteContact($id),
            'ContactType' => $this->contactTypeService->deleteContactType($id),
            default => throw new Exception("Invalid resource type for ContactFacade 'delete': $resourceType"),
        };
    }

    /**
     * Disables a resource using the appropriate service.
     * (Note: These services don't have a 'disable' method, so we'll throw an exception)
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        return throw new Exception("Invalid or unsupported resource type for ContactFacade 'disable': $resourceType");
    }
}