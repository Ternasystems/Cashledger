<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Audits;
use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Audit;
use Exception;
use TS_Utility\Enums\OrderEnum;

class AuditRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Audits
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Audits ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Audit
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Audit ? $entity : null;
    }

    public function OrderBy(Collectable $audits, array $properties, array $orderBy = [OrderEnum::ASC]): ?Audits
    {
        if (!$audits instanceof Audits)
            throw new Exception("audits must be instance of Audits");

        $collection = parent::OrderBy($audits, $properties, $orderBy);
        return $collection instanceof Audits ? $collection : null;
    }
}