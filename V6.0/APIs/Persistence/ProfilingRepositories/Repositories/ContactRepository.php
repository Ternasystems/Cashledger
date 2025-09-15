<?php

namespace API_ProfilingRepositories;

use API_Assets\DTOException;
use API_DTORepositories\Repository;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\Contacts;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Contact;
use Closure;

class ContactRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Contact
    {
        $entity = parent::first($predicate);
        return $entity instanceof Contact ? $entity : null;
    }

    public function getAll(): ?Contacts
    {
        $collection = parent::getAll();
        return $collection instanceof Contacts ? $collection : null;
    }

    public function getById(string $id): ?Contact
    {
        $entity = parent::getById($id);
        return $entity instanceof Contact ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Contacts
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Contacts ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Contact
    {
        $entity = parent::last($predicate);
        return $entity instanceof Contact ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Contact)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Contact)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}