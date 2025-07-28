<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Audits;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Audit;
use Closure;

class AuditRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::first($predicate);
        return $entity instanceof Audit ? $entity : null;
    }

    public function GetAll(): ?Audits
    {
        $collection = parent::GetAll();
        return $collection instanceof Audits ? $collection : null;
    }

    public function GetById(string $id): ?Audit
    {
        $entity = parent::GetById($id);
        return $entity instanceof Audit ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Audits
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Audits ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::last($predicate);
        return $entity instanceof Audit ? $entity : null;
    }
}