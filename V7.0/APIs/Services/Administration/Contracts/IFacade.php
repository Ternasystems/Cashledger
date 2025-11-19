<?php

namespace API_Administration_Contract;

use API_Administration_Service\ReloadMode;

/**
 * A generic, root interface for all Facade services.
 * It establishes a common set of CRUD-like methods that
 * composite services (facades) should provide.
 */
interface IFacade
{
    /**
     * Gets a resource from the appropriate service.
     *
     * @param string $resourceType (e.g., 'Profile', 'Gender', 'Civility')
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return mixed The result from the specific service.
     */
    public function get(string $resourceType, ?array $filter, int $page, int $pageSize, ReloadMode $reloadMode): mixed;

    /**
     * Creates a new resource using the appropriate service.
     *
     * @param string $resourceType
     * @param array $data
     * @return mixed The newly created entity.
     */
    public function set(string $resourceType, array $data): mixed;

    /**
     * Updates an existing resource using the appropriate service.
     *
     * @param string $resourceType
     * @param string $id
     * @param array $data
     * @return mixed The updated entity.
     */
    public function put(string $resourceType, string $id, array $data): mixed;

    /**
     * Deletes (soft) a resource using the appropriate service.
     *
     * @param string $resourceType
     * @param string $id
     * @return bool True on success.
     */
    public function delete(string $resourceType, string $id): bool;

    /**
     * Disables a resource using the appropriate service.
     *
     * @param string $resourceType
     * @param string $id
     * @return bool True on success.
     */
    public function disable(string $resourceType, string $id): bool;
}