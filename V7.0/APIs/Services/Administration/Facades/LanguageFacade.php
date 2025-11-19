<?php

namespace API_Administration_Facade;

use API_Administration_Contract\IFacade;
use API_Administration_Contract\ILanguageService;
use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Model\Language;
use Exception;

/**
 * This is an "Adapter Facade" for the LanguageService.
 * It implements the generic IFacade interface and translates
 * the generic calls (get, set, put) to the specific
 * methods on ILanguageService (getLanguages, setLanguage, etc.).
 */
class LanguageFacade implements IFacade
{
    public function __construct(protected ILanguageService $languageService)
    {
    }

    /**
     * Gets resources. We ignore $resourceType because this facade
     * only handles languages.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): null|Languages|Language
    {
        return $this->languageService->getLanguages($filter, $page, $pageSize, $reloadMode);
    }

    /**
     * Creates a new resource.
     */
    public function set(string $resourceType, array $data): Language
    {
        return $this->languageService->setLanguage($data);
    }

    /**
     * Updates an existing resource.
     */
    public function put(string $resourceType, string $id, array $data): ?Language
    {
        return $this->languageService->putLanguage($id, $data);
    }

    /**
     * Deletes (soft) a resource.
     */
    public function delete(string $resourceType, string $id): bool
    {
        return $this->languageService->deleteLanguage($id);
    }

    /**
     * Disables a resource.
     * @throws Exception
     */
    public function disable(string $resourceType, string $id): bool
    {
        // The ILanguageService doesn't have a 'disable' method,
        // so we throw an exception, just like in our other facades.
        return throw new Exception("Invalid or unsupported action 'disable' for LanguageFacade");
    }
}