<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Countries;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Country;
use API_DTORepositories_Model\DTOBase;
use Closure;

class CountryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Country
    {
        $entity = parent::first($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    public function getAll(): ?Countries
    {
        $collection = parent::getAll();
        return $collection instanceof Countries ? $collection : null;
    }

    public function getById(string $id): ?Country
    {
        $entity = parent::getById($id);
        return $entity instanceof Country ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Countries
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Countries ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Country
    {
        $entity = parent::last($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Country)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Country)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}