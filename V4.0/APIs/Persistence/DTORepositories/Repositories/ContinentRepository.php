<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\Continents;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Continent;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ContinentRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    public function GetAll(): ?Continents
    {
        $collection = parent::GetAll();
        return $collection instanceof Continents ? $collection : null;
    }

    public function GetById(string $id): ?Continent
    {
        $entity = parent::GetById($id);
        return $entity instanceof Continent ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Continents
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Continents ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    public function OrderBy(Collectable $continents, array $properties, array $orderBy = [OrderEnum::ASC]): ?Continents
    {
        if (!$continents instanceof Continents)
            throw new Exception("Continents must be instance of Continents");

        $collection = parent::OrderBy($continents, $properties, $orderBy);
        return $collection instanceof Continents ? $collection : null;
    }
}