<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\Languages;
use API_DTOEntities_Model\Language;

interface ILanguageService
{
    /**
     * Gets a paginated list of Language entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return Language|Languages|null An associative array containing 'data' and 'total'.
     */
    public function getLanguages(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Language|Languages|null;

    /**
     * Creates a new Language and assigns roles.
     *
     * @param array $data
     * @return Language The newly created Language entity.
     */
    public function setLanguage(array $data): Language;

    /**
     * Updates an existing Language
     *
     * @param string $id
     * @param array $data
     * @return Language|null
     */
    public function putLanguage(string $id, array $data): ?Language;

    /**
     * Deletes a Language and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteLanguage(string $id): bool;
}