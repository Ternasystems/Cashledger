<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Civilities;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Civility;
use Exception;
use TS_Utility\Enums\OrderEnum;

class CivilityRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    public function GetAll(): ?Civilities
    {
        $collection = parent::GetAll();
        return $collection instanceof Civilities ? $collection : null;
    }

    public function GetById(string $id): ?Civility
    {
        $entity = parent::GetById($id);
        return $entity instanceof Civility ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Civilities
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Civilities ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    public function OrderBy(Collectable $civilities, array $properties, array $orderBy = [OrderEnum::ASC]): ?Civilities
    {
        if (!$civilities instanceof Civilities)
            throw new Exception("Civilities must be instance of Civilities");

        $collection = parent::OrderBy($civilities, $properties, $orderBy);
        return $collection instanceof Civilities ? $collection : null;
    }
}