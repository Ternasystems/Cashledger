<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\ContactTypes;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\ContactType;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ContactTypeRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ContactType
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function GetAll(): ?ContactTypes
    {
        $collection = parent::GetAll();
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function GetById(string $id): ?ContactType
    {
        $entity = parent::GetById($id);
        return $entity instanceof ContactType ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ContactTypes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ContactTypes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ContactType
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ContactType ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $contactTypes, array $properties, array $orderBy = [OrderEnum::ASC]): ?ContactTypes
    {
        if (!$contactTypes instanceof ContactTypes)
            throw new Exception("ContactTypes must be instance of ContactTypes");

        $collection = parent::OrderBy($contactTypes, $properties, $orderBy);
        return $collection instanceof ContactTypes ? $collection : null;
    }
}