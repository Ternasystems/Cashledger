<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Cities;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\City;
use Closure;

class CityRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Cities
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Cities ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?City
    {
        $entity = parent::last($predicate);
        return $entity instanceof City ? $entity : null;
    }
}