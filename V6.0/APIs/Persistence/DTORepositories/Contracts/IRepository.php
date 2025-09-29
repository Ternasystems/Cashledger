<?php

namespace API_DTORepositories_Contract;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use Closure;

/**
 * A generic contract for a repository, defining the standard data access methods.
 *
 * @template T of DTOBase
 * @template TCollection of Collectable
 */
interface IRepository
{
    /**
     * Finds the first entity in the collection, optionally matching a predicate.
     * @param array|null $whereClause
     * @return object|null
     */
    public function first(?array $whereClause = null): ?object;

    /**
     * Gets all entities managed by the repository.
     * @param int|null $limit
     * @param int|null $offset
     * @return Collectable|null
     */
    public function getAll(?int $limit = null, ?int $offset = null): ?Collectable;

    /**
     * Gets a single entity by its unique identifier.
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object;

    /**
     * Finds all entities that match a given predicate.
     * @param array|null $whereClause
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $orderBy
     * @return Collectable|null
     */
    public function getBy(?array $whereClause = null, ?int $limit = null, ?int $offset = null, ?array $orderBy = null): ?Collectable;

    /**
     * Finds the last entity in the collection, optionally matching a predicate.
     * @param array|null $whereClause
     * @return object|null
     */
    public function last(?array $whereClause = null): ?object;

    /**
     * Calls the corresponding 'Insert' stored procedure for the entity.
     * @param DTOBase $entity The entity object to add.
     */
    public function add(DTOBase $entity): void;

    /**
     * Calls the corresponding 'Delete' stored procedure for the entity.
     * @param string $id
     */
    public function remove(string $id): void;

    /**
     * Calls the corresponding 'Disable' (soft delete) stored procedure for the entity.
     * @param string $id
     */
    public function deactivate(string $id): void;

    /**
     * Calls the corresponding 'Update' stored procedure for the entity.
     * @param DTOBase $entity The entity object to update.
     */
    public function update(DTOBase $entity): void;
}