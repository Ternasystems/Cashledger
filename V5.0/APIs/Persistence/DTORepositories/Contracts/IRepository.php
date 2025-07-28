<?php

namespace API_DTORepositories_Contract;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use Closure;

/**
 * A generic contract for a repository.
 *
 * @template T of DTOBase
 * @template TCollection of Collectable
 */
interface IRepository
{
    /**
     * @param Closure|null $predicate
     * @return object|null
     */
    public function first(?Closure $predicate = null): ?object;

    /**
     * @return Collectable|null
     */
    public function getAll(): ?Collectable;

    /**
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object;

    /**
     * @param Closure $predicate
     * @return Collectable|null
     */
    public function getBy(Closure $predicate): ?Collectable;

    /**
     * @param Closure|null $predicate
     * @return object|null
     */
    public function last(?Closure $predicate = null): ?object;

    public function add(array $args): void;
    public function remove(string $id): void;
    public function deactivate(string $id): void;
    public function update(array $args): void;
}