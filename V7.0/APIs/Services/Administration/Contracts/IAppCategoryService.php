<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;
use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Model\AppCategory;

interface IAppCategoryService
{
    /**
     * Gets a paginated list of AppCategory entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return AppCategory|AppCategories|null An associative array containing 'data' and 'total'.
     */
    public function getAppCategories(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): AppCategory|AppCategories|null;

    /**
     * Creates a new AppCategory and assigns roles.
     *
     * @param array $data
     * @return AppCategory The newly created AppCategory entity.
     */
    public function setAppCategory(array $data): AppCategory;

    /**
     * Updates an existing AppCategory
     *
     * @param string $id
     * @param array $data
     * @return AppCategory|null
     */
    public function putAppCategory(string $id, array $data): ?AppCategory;

    /**
     * Deletes a AppCategory and its associated role relations.
     *
     * @param string $id The ID of the credential to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteAppCategory(string $id): bool;
}