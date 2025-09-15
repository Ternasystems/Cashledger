<?php

namespace API_DTORepositories;

use API_Assets\DTOException;
use API_DTORepositories_Collection\Audits;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Audit;
use API_DTORepositories_Model\DTOBase;
use Closure;

class AuditRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function first(?Closure $predicate = null): ?Audit
    {
        $entity = parent::first($predicate);
        return $entity instanceof Audit ? $entity : null;
    }

    public function getAll(): ?Audits
    {
        $collection = parent::getAll();
        return $collection instanceof Audits ? $collection : null;
    }

    public function getById(string $id): ?Audit
    {
        $entity = parent::getById($id);
        return $entity instanceof Audit ? $entity : null;
    }

    public function getBy(Closure $predicate): ?Audits
    {
        $collection = parent::getBy($predicate);
        return $collection instanceof Audits ? $collection : null;
    }

    public function last(?Closure $predicate = null): ?Audit
    {
        $entity = parent::last($predicate);
        return $entity instanceof Audit ? $entity : null;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Audit)) {
            throw new DTOException('invalid_argument');
        }
        parent::add($entity);
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Audit)) {
            throw new DTOException('invalid_argument');
        }
        parent::update($entity);
    }
}