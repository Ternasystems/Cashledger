<?php

namespace API_DTORepositories_Contract;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use TS_Domain\Interfaces\ISpecification;

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
     * @return DTOBase|null
     */
    public function first(?array $whereClause = null): ?DTOBase;

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
     * @return DTOBase|null
     */
    public function getById(string $id): ?DTOBase;

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
     * Finds entities that satisfy a given specification.
     *
     * @param ISpecification $specification The specification to apply.
     * @return Collectable|null A collection of matching entities.
     */
    public function find(ISpecification $specification): ?Collectable;

    /**
     * Finds the last entity in the collection, optionally matching a predicate.
     * @param array|null $whereClause
     * @return DTOBase|null
     */
    public function last(?array $whereClause = null): ?DTOBase;

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