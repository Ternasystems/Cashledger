<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\App;
use API_DTORepositories_Model\DTOBase;
use Closure;

class AppRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?App
    {
        $entity = parent::first($predicate);
        return $entity instanceof App ? $entity : null;
    }

    public function getAll(): ?Apps
    {
        $collection = parent::getAll();
        return $collection instanceof Apps ? $collection : null;
    }

    public function getById(string $id): ?App
    {
        $entity = parent::getById($id);
        return $entity instanceof App ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Apps
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Apps ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?App
    {
        $entity = parent::last($predicate);
        return $entity instanceof App ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof App)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof App)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}