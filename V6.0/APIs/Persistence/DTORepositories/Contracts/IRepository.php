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
     * @param Closure|null $predicate
     * @return object|null
     */
    public function first(?Closure $predicate = null): ?object;

    /**
     * Gets all entities managed by the repository.
     * @return Collectable|null
     */
    public function getAll(): ?Collectable;

    /**
     * Gets a single entity by its unique identifier.
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object;

    /**
     * Finds all entities that match a given predicate.
     * @param Closure $predicate
     * @return Collectable|null
     */
    public function getBy(Closure $predicate): ?Collectable;

    /**
     * Finds the last entity in the collection, optionally matching a predicate.
     * @param Closure|null $predicate
     * @return object|null
     */
    public function last(?Closure $predicate = null): ?object;

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