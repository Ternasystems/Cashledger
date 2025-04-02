<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\Cities;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\City;
use Exception;
use TS_Utility\Enums\OrderEnum;

class CityRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof City ? $entity : null;
    }

    public function GetAll(): ?Cities
    {
        $collection = parent::GetAll();
        return $collection instanceof Cities ? $collection : null;
    }

    public function GetById(string $id): ?City
    {
        $entity = parent::GetById($id);
        return $entity instanceof City ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Cities
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Cities ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof City ? $entity : null;
    }

    public function OrderBy(Collectable $cities, array $properties, array $orderBy = [OrderEnum::ASC]): ?Cities
    {
        if (!$cities instanceof Cities)
            throw new Exception("Cities must be instance of Cities");

        $collection = parent::OrderBy($cities, $properties, $orderBy);
        return $collection instanceof Cities ? $collection : null;
    }
}